<?php

namespace App\Services;

use App\Models\Product;

class ProductSearchService
{
    public function search(array $intent)
    {
        $query = Product::query();

        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */
                $categories = $this->extractCategories($intent);

        $primaryCategory =
        $intent['category']['primary'] ?? null;

            if (!empty($categories)) {

                $query->whereHas('category', function ($q) use ($categories) {

                    $q->whereIn('name', $categories);

                });
            }

        /*
        |--------------------------------------------------------------------------
        | BRAND
        |--------------------------------------------------------------------------
        */
        if (!empty($intent['brand'])) {
            $query->whereHas('brand', function ($q) use ($intent) {
                $q->where('name', $intent['brand']);
            });
        }



       /*
        |--------------------------------------------------------------------------
        | REQUIRED ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        if (!empty($intent['required_attributes'])) {

                foreach ($intent['required_attributes'] as $name => $value) {

                    $query->whereHas('attributes', function ($q) use ($name, $value) {

                        $q->whereRaw(
                            'LOWER(attribute_name) = ?',
                            [strtolower($name)]
                        );

                        if (is_bool($value)) {

                            if ($value === true) {
                                $q->whereNotNull('attribute_value');
                            }

                        } else {

                           
                  $requestedNumber = $this->extractNumber((string) $value);

                                if ($requestedNumber !== null) {

                                    $q->whereRaw(
                                        'CAST(REGEXP_SUBSTR(attribute_value, "[0-9]+") AS UNSIGNED) >= ?',
                                        [$requestedNumber]
                                    );

                                } else {

                                    $q->where(
                                        'attribute_value',
                                        'LIKE',
                                        "%{$value}%"
                                    );
                                }


                        }

                        });
                }
        }



        /*
        |--------------------------------------------------------------------------
        | BUDGET
        |--------------------------------------------------------------------------
        */
        if (!empty($intent['budget_max'])) {
            $query->where('sale_price', '<=', $intent['budget_max']);
        }

        /*
        |--------------------------------------------------------------------------
        | KEYWORDS
        |--------------------------------------------------------------------------
        */
        if (!empty($intent['search_keywords'])) {

            $query->where(function ($q) use ($intent) {

                foreach ($intent['search_keywords'] as $keyword) {

                    $q->orWhere('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('attributes', function ($attr) use ($keyword) {
                            $attr->where('attribute_value', 'LIKE', "%{$keyword}%");
                        });
                }
            });
        }

        $products = $query
            ->with([
                'brand',
                'category',
                'images',
                'attributes'
            ])
            ->get();

        /*
        |--------------------------------------------------------------------------
        | FALLBACK
        |--------------------------------------------------------------------------
        */
        if ($products->isEmpty()) {

            $products = Product::with([
                'brand',
                'category',
                'images',
                'attributes'
            ])->get();
        }

        return $this->rankProducts(
            $products,
            $intent,
            $intent['category'] ?? []
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY EXTRACTOR
    |--------------------------------------------------------------------------
    */
                private function extractCategories(array $intent): array
            {
                if (empty($intent['category'])) {
                    return [];
                }

                if (is_string($intent['category'])) {
                    return [$intent['category']];
                }

                $categories = [];

                if (!empty($intent['category']['primary'])) {
                    $categories[] = $intent['category']['primary'];
                }

                if (!empty($intent['category']['alternatives'])) {
                    $categories = array_merge(
                        $categories,
                        $intent['category']['alternatives']
                    );
                }

                return array_unique($categories);
            }

    /*
    |--------------------------------------------------------------------------
    | RANKING
    |--------------------------------------------------------------------------
    */
    private function rankProducts(
        $products,
        array $intent,
         array $categoryData = []
    ) {
        return $products
            ->map(function ($product) use ($intent, $categoryData) {

                $categoryScore = $this->scoreCategoryMatch(
                    $product,
                    $categoryData
                );

                $keywordScore = $this->scoreKeywords(
                    $product,
                    $intent
                );

                $featureScore = $this->scorePriorityFeatures(
                    $product,
                    $intent
                );

                $valueScore = $this->scoreValue(
                    $product,
                    $intent
                );

                $categorySpecificScore =
                    $this->scoreCategorySpecific(
                        $product,
                        $categoryData
                    );

                $preferredAttributeScore =
                $this->scorePreferredAttributes(
                    $product,
                    $intent,
                    $categoryData
                );



                $total =
                    $categoryScore +
                    $keywordScore +
                    $featureScore +
                    $valueScore +
                    $categorySpecificScore +
                    $preferredAttributeScore;

                $product->ai_score = $total;

                return $product;
            })
            ->sortByDesc('ai_score')
            ->take(20)
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | CATEGORY MATCH
    |--------------------------------------------------------------------------
    */
   private function scoreCategoryMatch(
    $product,
    array $categoryData
)
{
    $productCategory =
        strtolower($product->category->name ?? '');

    $primary =
        strtolower($categoryData['primary'] ?? '');

    $alternatives =
        collect($categoryData['alternatives'] ?? [])
            ->map(fn ($item) => strtolower($item))
            ->toArray();

    if ($productCategory === $primary) {
        return 100;
    }

    if (in_array($productCategory, $alternatives)) {
        return 60;
    }

    return 0;
}

    /*
    |--------------------------------------------------------------------------
    | CATEGORY SPECIFIC SCORING
    |--------------------------------------------------------------------------
    */
    private function scoreCategorySpecific(
        $product,
        array $categoryData
    ) {
        $category = strtolower($categoryData['primary'] ?? '');

        if (str_contains($category, 'computer')) {
            return $this->scoreComputer($product);
        }

        if (str_contains($category, 'phone')) {
            return $this->scorePhone($product);
        }

        if (str_contains($category, 'speaker')) {
            return $this->scoreSpeaker($product);
        }

        if (str_contains($category, 'charger')) {
            return $this->scoreCharger($product);
        }

        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | COMPUTER SCORE
    |--------------------------------------------------------------------------
    */
    private function scoreComputer($product)
    {
        $score = 0;

        $processor = strtolower(
            $this->getAttributeValue($product, 'processor')
        );

        $graphics = strtolower(
            $this->getAttributeValue($product, 'graphics')
        );

        $ram = strtolower(
            $this->getAttributeValue($product, 'ram')
        );

        if (str_contains($processor, 'i9') || str_contains($processor, 'ryzen 9')) {
            $score += 40;
        } elseif (str_contains($processor, 'i7') || str_contains($processor, 'ryzen 7')) {
            $score += 30;
        } elseif (str_contains($processor, 'i5') || str_contains($processor, 'ryzen 5')) {
            $score += 20;
        }

        if (str_contains($graphics, 'rtx')) {
            $score += 25;
        }

        if (str_contains($ram, '32')) {
            $score += 20;
        } elseif (str_contains($ram, '16')) {
            $score += 15;
        }

        return $score;
    }

    /*
    |--------------------------------------------------------------------------
    | PHONE SCORE
    |--------------------------------------------------------------------------
    */
    private function scorePhone($product)
    {
        $score = 0;

        $camera = strtolower(
            $this->getAttributeValue($product, 'camera')
        );

        $battery = strtolower(
            $this->getAttributeValue($product, 'battery')
        );

        $ram = strtolower(
            $this->getAttributeValue($product, 'ram')
        );

        if (
            str_contains($camera, '64') ||
            str_contains($camera, '108')
        ) {
            $score += 30;
        }

        if (str_contains($battery, '5000')) {
            $score += 25;
        }

        if (
            str_contains($ram, '8') ||
            str_contains($ram, '12')
        ) {
            $score += 20;
        }

        return $score;
    }

    /*
    |--------------------------------------------------------------------------
    | SPEAKER SCORE
    |--------------------------------------------------------------------------
    */
    private function scoreSpeaker($product)
    {
        $score = 0;

        $text = strtolower(
            $product->attributes->pluck('attribute_value')->implode(' ')
        );

        if (str_contains($text, 'bluetooth')) {
            $score += 20;
        }

        if (str_contains($text, 'bass')) {
            $score += 20;
        }

        return $score;
    }

    /*
    |--------------------------------------------------------------------------
    | CHARGER SCORE
    |--------------------------------------------------------------------------
    */
    private function scoreCharger($product)
    {
        $score = 0;

        $text = strtolower(
            $product->attributes->pluck('attribute_value')->implode(' ')
        );

        if (str_contains($text, 'fast charge')) {
            $score += 25;
        }

        if (str_contains($text, 'usb-c')) {
            $score += 15;
        }

        return $score;
    }

    /*
    |--------------------------------------------------------------------------
    | FEATURE SCORE
    |--------------------------------------------------------------------------
    */
    private function scorePriorityFeatures(
        $product,
        array $intent
    ) {
        $score = 0;

        foreach ($intent['priority_features'] ?? [] as $feature) {

            foreach ($product->attributes as $attribute) {

                if (
                    str_contains(
                        strtolower($attribute->attribute_value),
                        strtolower($feature)
                    )
                ) {
                    $score += 15;
                }
            }
        }

        return $score;
    }


/*
    |--------------------------------------------------------------------------
    | PREFERRED ATTRIBUTES SCORE
    |--------------------------------------------------------------------------
    */

                private function scorePreferredAttributes(
                    $product,
                    array $intent,
                    array $categoryData
                ) {
                    if (!$categoryData) {
                        return 0;
                    }

                    $categoryKey = collect(
                        array_keys($intent['preferred_attributes_by_category'] ?? [])
                    )->first(function ($key) use ($categoryData) {
                        return strtolower($key) === strtolower($categoryData['primary'] ?? '');
                    });

                    $preferences =
                        $intent['preferred_attributes_by_category'][$categoryKey]
                        ?? [];

                    $score = 0;

                    foreach ($preferences as $name => $value) {

                        foreach ($product->attributes as $attribute) {

                            if (
                        strtolower($attribute->attribute_name) === strtolower($name)
                    ) {

                        $requestedNumber =
                            $this->extractNumber((string) $value);

                        $productNumber =
                            $this->extractNumber(
                                $attribute->attribute_value
                            );

                        if (
                            $requestedNumber !== null &&
                            $productNumber !== null
                        ) {

                            if ($productNumber >= $requestedNumber) {
                                $score += 20;
                            }

                        } elseif (
                            str_contains(
                                strtolower($attribute->attribute_value),
                                strtolower((string) $value)
                            )
                        ) {

                            $score += 20;
                        }
                    }

                        }
                    }

                    return $score;
                }




    /*
    |--------------------------------------------------------------------------
    | KEYWORD SCORE
    |--------------------------------------------------------------------------
    */
    private function scoreKeywords(
    $product,
    array $intent
) {
    $score = 0;

    foreach ($intent['search_keywords'] ?? [] as $keyword) {

        $keyword = strtolower($keyword);

        if (
            str_contains(
                strtolower($product->name),
                $keyword
            )
        ) {
            $score += 20;
        }

        if (
            str_contains(
                strtolower($product->description),
                $keyword
            )
        ) {
            $score += 10;
        }

        foreach ($product->attributes as $attribute) {

            if (
                str_contains(
                    strtolower($attribute->attribute_value),
                    $keyword
                )
            ) {
                $score += 5;
            }
        }
    }

    return $score;
}

    /*
    |--------------------------------------------------------------------------
    | VALUE SCORE
    |--------------------------------------------------------------------------
    */
    private function scoreValue(
        $product,
        array $intent
    ) {
        if (empty($intent['budget_max'])) {
            return 0;
        }

        $ratio =
            $product->sale_price /
            $intent['budget_max'];

        if ($ratio <= 0.6) return 30;
        if ($ratio <= 0.8) return 20;
        if ($ratio <= 1.0) return 10;

        return 0;
    }

    /*
    |--------------------------------------------------------------------------
    | ATTRIBUTE HELPER
    |--------------------------------------------------------------------------
    */
    private function getAttributeValue(
        $product,
        string $attributeName
    ) {
        $attribute = $product->attributes->first(function ($item) use ($attributeName) {
            return strtolower($item->attribute_name)
                === strtolower($attributeName);
        });

        return $attribute->attribute_value ?? '';
    }


private function extractNumber(string $value): ?float
{
    preg_match('/\d+(\.\d+)?/', $value, $matches);

    return isset($matches[0])
        ? (float) $matches[0]
        : null;
}





}