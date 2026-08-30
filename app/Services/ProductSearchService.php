<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductSearchService
{
    /**
     * Search the catalog using hard customer constraints,
     * then rank the matching products using soft intent signals.
     */
    public function search(array $intent): Collection
    {
        $query = Product::query()
            ->with([
                'brand',
                'category',
                'images',
                'attributes',
            ]);

        /*
        |--------------------------------------------------------------------------
        | CATEGORY — HARD FILTER
        |--------------------------------------------------------------------------
        */

        $categories = $this->extractCategories($intent);

        if (!empty($categories)) {
            $query->whereHas('category', function ($q) use ($categories) {
                $q->whereIn('name', $categories);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | BRAND — HARD FILTER
        |--------------------------------------------------------------------------
        */

        if (!empty($intent['brand'])) {
            $brand = $intent['brand'];

            $query->whereHas('brand', function ($q) use ($brand) {
                $q->where('name', $brand);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | BUDGET — HARD FILTER
        |--------------------------------------------------------------------------
        |
        | Effective price:
        |
        | sale_price when present and greater than zero,
        | otherwise normal price.
        |
        */

        if (
            isset($intent['budget_max']) &&
            is_numeric($intent['budget_max'])
        ) {
            $budget = (float) $intent['budget_max'];

            $query->where(function ($q) use ($budget) {

                $q->where(function ($saleQuery) use ($budget) {
                    $saleQuery
                        ->whereNotNull('sale_price')
                        ->where('sale_price', '>', 0)
                        ->where('sale_price', '<=', $budget);
                });

                $q->orWhere(function ($priceQuery) use ($budget) {
                    $priceQuery
                        ->where(function ($noSaleQuery) {
                            $noSaleQuery
                                ->whereNull('sale_price')
                                ->orWhere('sale_price', '<=', 0);
                        })
                        ->where('price', '<=', $budget);
                });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | REQUIRED ATTRIBUTES — HARD FILTER
        |--------------------------------------------------------------------------
        |
        | These came from explicit customer requirements.
        |
        | Every required attribute must match.
        |
        */

        foreach ($intent['required_attributes'] ?? [] as $name => $value) {

            $query->whereHas(
                'attributes',
                function ($q) use ($name, $value) {

                    $q->whereRaw(
                        'LOWER(attribute_name) = ?',
                        [strtolower(trim($name))]
                    );

                    $this->applyRequiredAttributeValue(
                        $q,
                        $value
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GET CANDIDATES
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | If nothing matches, we return an empty collection.
        |
        | We DO NOT remove the customer's requirements and retrieve
        | unrelated products.
        |
        */

        $products = $query->get();

        if ($products->isEmpty()) {
            return collect();
        }

        /*
        |--------------------------------------------------------------------------
        | RANK MATCHING PRODUCTS
        |--------------------------------------------------------------------------
        */

        return $this->rankProducts(
            $products,
            $intent
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY EXTRACTION
    |--------------------------------------------------------------------------
    */

    private function extractCategories(array $intent): array
    {
        $category = $intent['category'] ?? null;

        if (!$category) {
            return [];
        }

        /*
         * Backward compatibility while local data/testing may still
         * produce the older string structure.
         */
        if (is_string($category)) {
            return [$category];
        }

        if (!is_array($category)) {
            return [];
        }

        $categories = [];

        if (!empty($category['primary'])) {
            $categories[] = $category['primary'];
        }

        if (
            !empty($category['alternatives']) &&
            is_array($category['alternatives'])
        ) {
            $categories = array_merge(
                $categories,
                $category['alternatives']
            );
        }

        return array_values(
            array_unique(
                array_filter($categories)
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | REQUIRED ATTRIBUTE VALUE
    |--------------------------------------------------------------------------
    */

    private function applyRequiredAttributeValue(
        $query,
        mixed $value
    ): void {

        /*
         * Boolean requirement
         */
        if (is_bool($value)) {

            if ($value) {
                $query->whereNotNull('attribute_value')
                    ->where('attribute_value', '!=', '');
            }

            return;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return;
        }

        /*
         * Numeric specification.
         *
         * Examples:
         *
         * 8 GB
         * 512 GB
         * 5000 mAh
         * 120 Hz
         *
         * Products with equal OR greater numeric values qualify.
         */
        $requestedNumber = $this->extractNumber($value);

        if ($requestedNumber !== null) {

            $query->whereRaw(
                'CAST(REGEXP_SUBSTR(attribute_value, "[0-9]+(\\\\.[0-9]+)?") AS DECIMAL(12,2)) >= ?',
                [$requestedNumber]
            );

            return;
        }

        /*
         * Textual technical requirement.
         *
         * Examples:
         *
         * HDMI
         * 5G
         * OLED
         * USB-C
         */
        $query->whereRaw(
            'LOWER(attribute_value) LIKE ?',
            ['%' . strtolower($value) . '%']
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PRODUCT RANKING
    |--------------------------------------------------------------------------
    */

    private function rankProducts(
        Collection $products,
        array $intent
    ): Collection {

        return $products
            ->map(function ($product) use ($intent) {

                $score = 0;

                /*
                 * Category relevance
                 */
                $score += $this->scoreCategory(
                    $product,
                    $intent
                );

                /*
                 * Preferred technical attributes
                 */
                $score += $this->scorePreferredAttributes(
                    $product,
                    $intent
                );

                /*
                 * Search keywords
                 */
                $score += $this->scoreKeywords(
                    $product,
                    $intent
                );

                /*
                 * Use case / priority features
                 */
                $score += $this->scoreIntentSignals(
                    $product,
                    $intent
                );

                /*
                 * Budget value
                 */
                $score += $this->scoreValue(
                    $product,
                    $intent
                );

                $product->ai_score = $score;

                return $product;
            })
            ->sortByDesc('ai_score')
            ->take(20)
            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY SCORE
    |--------------------------------------------------------------------------
    */

    private function scoreCategory(
        $product,
        array $intent
    ): int {

        $productCategory = strtolower(
            $product->category->name ?? ''
        );

        $primary = strtolower(
            $intent['category']['primary'] ?? ''
        );

        if (
            $primary !== '' &&
            $productCategory === $primary
        ) {
            return 100;
        }

        $alternatives = collect(
            $intent['category']['alternatives'] ?? []
        )
            ->map(fn ($category) => strtolower($category))
            ->toArray();

        if (in_array($productCategory, $alternatives, true)) {
            return 60;
        }

        return 0;
    }


    /*
    |--------------------------------------------------------------------------
    | PREFERRED ATTRIBUTE SCORE
    |--------------------------------------------------------------------------
    |
    | Preferences are selected according to the PRODUCT'S category.
    |
    | This matters for multi-category searches.
    |
    */

    private function scorePreferredAttributes(
        $product,
        array $intent
    ): int {

        $preferencesByCategory =
            $intent['preferred_attributes_by_category'] ?? [];

        if (empty($preferencesByCategory)) {
            return 0;
        }

        $productCategory =
            $product->category->name ?? null;

        if (!$productCategory) {
            return 0;
        }

        $categoryKey = collect(
            array_keys($preferencesByCategory)
        )->first(function ($category) use ($productCategory) {
            return strtolower($category)
                === strtolower($productCategory);
        });

        if (!$categoryKey) {
            return 0;
        }

        $preferences =
            $preferencesByCategory[$categoryKey] ?? [];

        $score = 0;

        foreach ($preferences as $name => $requestedValue) {

            $productValue =
                $this->getAttributeValue(
                    $product,
                    $name
                );

            if ($productValue === '') {
                continue;
            }

            if (
                $this->attributeSatisfiesPreference(
                    $productValue,
                    $requestedValue
                )
            ) {
                $score += 20;
            }
        }

        return $score;
    }


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE PREFERENCE MATCHING
    |--------------------------------------------------------------------------
    */

    private function attributeSatisfiesPreference(
        string $productValue,
        mixed $requestedValue
    ): bool {

        if (is_bool($requestedValue)) {
            return $requestedValue
                ? trim($productValue) !== ''
                : true;
        }

        $requestedValue = trim(
            (string) $requestedValue
        );

        if ($requestedValue === '') {
            return false;
        }

        $requestedNumber =
            $this->extractNumber($requestedValue);

        $productNumber =
            $this->extractNumber($productValue);

        if (
            $requestedNumber !== null &&
            $productNumber !== null
        ) {
            return $productNumber >= $requestedNumber;
        }

        return str_contains(
            strtolower($productValue),
            strtolower($requestedValue)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | KEYWORD SCORE
    |--------------------------------------------------------------------------
    |
    | Keywords are SOFT signals.
    |
    | They never eliminate a product.
    |
    */

    private function scoreKeywords(
        $product,
        array $intent
    ): int {

        $keywords =
            $intent['search_keywords'] ?? [];

        if (empty($keywords)) {
            return 0;
        }

        $score = 0;

        $name = strtolower(
            $product->name ?? ''
        );

        $description = strtolower(
            $product->description ?? ''
        );

        $attributeText = strtolower(
            $product->attributes
                ->pluck('attribute_value')
                ->filter()
                ->implode(' ')
        );

        foreach ($keywords as $keyword) {

            $keyword = strtolower(
                trim((string) $keyword)
            );

            if ($keyword === '') {
                continue;
            }

            if (str_contains($name, $keyword)) {
                $score += 15;
            }

            if (str_contains($description, $keyword)) {
                $score += 8;
            }

            if (str_contains($attributeText, $keyword)) {
                $score += 5;
            }
        }

        return $score;
    }


    /*
    |--------------------------------------------------------------------------
    | INTENT / USE CASE SCORE
    |--------------------------------------------------------------------------
    |
    | We intentionally keep this generic.
    |
    | No hardcoded SmartCart category names are used here.
    |
    | Use-case words and priority features are searched across actual
    | product information as soft relevance signals.
    |
    */

    private function scoreIntentSignals(
        $product,
        array $intent
    ): int {

        $signals = [];

        if (!empty($intent['use_case'])) {
            $signals[] = $intent['use_case'];
        }

        foreach (
            $intent['priority_features'] ?? []
            as $feature
        ) {
            $signals[] = $feature;
        }

        if (empty($signals)) {
            return 0;
        }

        $searchableText = strtolower(
            implode(' ', [
                $product->name ?? '',
                $product->description ?? '',
                $product->category->name ?? '',
                $product->brand->name ?? '',
                $product->attributes
                    ->pluck('attribute_value')
                    ->filter()
                    ->implode(' '),
            ])
        );

        $score = 0;

        foreach (array_unique($signals) as $signal) {

            $normalizedSignal = strtolower(
                str_replace(
                    '_',
                    ' ',
                    trim((string) $signal)
                )
            );

            if (
                $normalizedSignal !== '' &&
                str_contains(
                    $searchableText,
                    $normalizedSignal
                )
            ) {
                $score += 15;
            }
        }

        return $score;
    }


    /*
    |--------------------------------------------------------------------------
    | VALUE SCORE
    |--------------------------------------------------------------------------
    |
    | This is deliberately a small ranking bonus.
    |
    | Budget eligibility has ALREADY been enforced as a hard filter.
    |
    */

    private function scoreValue(
        $product,
        array $intent
    ): int {

        if (
            empty($intent['budget_max']) ||
            !is_numeric($intent['budget_max'])
        ) {
            return 0;
        }

        $budget =
            (float) $intent['budget_max'];

        if ($budget <= 0) {
            return 0;
        }

        $price =
            $this->effectivePrice($product);

        if ($price <= 0) {
            return 0;
        }

        $ratio = $price / $budget;

        /*
         * Give a modest value bonus.
         *
         * We don't want "cheapest" to dominate actual relevance.
         */
        if ($ratio <= 0.60) {
            return 12;
        }

        if ($ratio <= 0.80) {
            return 10;
        }

        if ($ratio <= 1.00) {
            return 8;
        }

        return 0;
    }


    /*
    |--------------------------------------------------------------------------
    | EFFECTIVE PRICE
    |--------------------------------------------------------------------------
    */

    private function effectivePrice($product): float
    {
        $salePrice =
            (float) ($product->sale_price ?? 0);

        if ($salePrice > 0) {
            return $salePrice;
        }

        return (float) ($product->price ?? 0);
    }


    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE HELPER
    |--------------------------------------------------------------------------
    */

    private function getAttributeValue(
        $product,
        string $attributeName
    ): string {

        $attribute = $product->attributes
            ->first(function ($item) use ($attributeName) {

                return strtolower(
                    trim($item->attribute_name)
                ) === strtolower(
                    trim($attributeName)
                );
            });

        return (string) (
            $attribute->attribute_value ?? ''
        );
    }


    /*
    |--------------------------------------------------------------------------
    | NUMBER EXTRACTION
    |--------------------------------------------------------------------------
    */

    private function extractNumber(
        string $value
    ): ?float {

        if (
            preg_match(
                '/\d+(?:\.\d+)?/',
                $value,
                $matches
            )
        ) {
            return (float) $matches[0];
        }

        return null;
    }
}