<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiIntentService
{
    private const MODEL_URL =
        'https://api.replicate.com/v1/models/openai/gpt-4o-mini/predictions';

    /**
     * Extract a clean shopping intent from the user's query.
     */
    public function extractIntent(string $query): array
    {
        $catalog = $this->buildCatalogContext();

        $intent = $this->requestIntent(
            $query,
            $catalog
        );

        if (!$intent) {
            return $this->emptyIntent();
        }

        $validation = $this->validateIntent(
            $intent,
            $catalog
        );

        /*
        |--------------------------------------------------------------------------
        | VALID INTENT
        |--------------------------------------------------------------------------
        */
        if ($validation['valid']) {
            return $this->normalizeIntent($intent);
        }

        /*
        |--------------------------------------------------------------------------
        | ONE CORRECTION ATTEMPT
        |--------------------------------------------------------------------------
        |
        | The AI returned something outside the current SmartCart catalog.
        | Give it the exact validation errors and current catalog vocabulary
        | and allow ONE correction attempt.
        |
        */

        $correctedIntent = $this->requestCorrection(
            $query,
            $intent,
            $validation['errors'],
            $catalog
        );

        if (!$correctedIntent) {
            return $this->emptyIntent();
        }

        $correctedValidation = $this->validateIntent(
            $correctedIntent,
            $catalog
        );

        if (!$correctedValidation['valid']) {

            Log::warning('SmartCart AI intent failed validation after retry.', [
                'query' => $query,
                'intent' => $correctedIntent,
                'errors' => $correctedValidation['errors'],
            ]);

            return $this->emptyIntent();
        }

        return $this->normalizeIntent($correctedIntent);
    }


    /**
     * Build the current catalog vocabulary directly from the database.
     *
     * Nothing here depends on hardcoded category or attribute names.
     */
    private function buildCatalogContext(): array
    {
        $categories = Category::query()
            ->with('products.attributes')
            ->get();

        $categoryNames = $categories
            ->pluck('name')
            ->filter()
            ->values()
            ->toArray();

        $brands = Brand::query()
            ->pluck('name')
            ->filter()
            ->values()
            ->toArray();

        $attributesByCategory = [];

        foreach ($categories as $category) {

            $attributes = $category->products
                ->flatMap(function ($product) {
                    return $product->attributes;
                })
                ->pluck('attribute_name')
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->toArray();

            $attributesByCategory[$category->name] = $attributes;
        }

        return [
            'categories' => $categoryNames,
            'brands' => $brands,
            'attributes' => $attributesByCategory,
        ];
    }


    /**
     * First AI intent extraction.
     */
    private function requestIntent(
        string $query,
        array $catalog
    ): ?array {

        $prompt = $this->buildIntentPrompt(
            $query,
            $catalog
        );

        return $this->callModel($prompt);
    }


    /**
     * Ask the AI to correct an invalid intent once.
     */
    private function requestCorrection(
        string $query,
        array $invalidIntent,
        array $errors,
        array $catalog
    ): ?array {

        $prompt = "
You are SmartCart's shopping intent correction engine.

The previous intent extraction contained values that are not valid
for the CURRENT SmartCart catalog.

Correct the intent using ONLY the catalog vocabulary supplied below.

==================================================
ORIGINAL USER QUERY
==================================================

{$query}

==================================================
CURRENT SMARTCART CATALOG
==================================================

" . $this->catalogPromptText($catalog) . "

==================================================
INVALID INTENT
==================================================

" . json_encode(
            $invalidIntent,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        ) . "

==================================================
VALIDATION ERRORS
==================================================

" . json_encode(
            $errors,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        ) . "

==================================================
CORRECTION RULES
==================================================

- Preserve the meaning of the original user query.
- Correct only values that conflict with the current catalog.
- Category names MUST exactly match CURRENT CATEGORIES.
- Brand MUST exactly match a CURRENT BRAND.
- Attribute names MUST exactly match attributes available for
  the relevant category.
- Never invent a category, brand, or attribute.
- Do not shorten or rename catalog values.
- Do not invent technical requirements that the user did not request.
- Return ONLY valid JSON.
- Do not include markdown.
- Do not include explanations.

Use exactly this structure:

{
  \"category\": {
    \"primary\": null,
    \"alternatives\": []
  },
  \"brand\": null,
  \"use_case\": null,
  \"budget_max\": null,
  \"budget_tier\": null,
  \"required_attributes\": {},
  \"preferred_attributes_by_category\": {},
  \"priority_features\": [],
  \"search_keywords\": []
}
";

        return $this->callModel($prompt);
    }


    /**
     * Build the main intent extraction prompt.
     */
    private function buildIntentPrompt(
        string $query,
        array $catalog
    ): string {

        return "
You are SmartCart's ecommerce shopping intent extraction engine.

Your ONLY job is to understand the customer's shopping request
and convert it into structured JSON.

You are NOT recommending products.

The SmartCart catalog below is the authoritative source of truth.

==================================================
CURRENT SMARTCART CATALOG
==================================================

" . $this->catalogPromptText($catalog) . "

==================================================
CRITICAL CATALOG RULES
==================================================

1. CATEGORY NAMES

- category.primary MUST be either null or EXACTLY one value from
  CURRENT CATEGORIES.

- category.alternatives may ONLY contain exact values from
  CURRENT CATEGORIES.

- Never shorten a category name.
- Never rename a category.
- Never substitute a synonym.
- Never singularize or pluralize a category.
- Never invent a category.

For example, if the current catalog contains a category whose exact
name describes smartphones, laptops, audio equipment, or another
product type, return the EXACT catalog string shown above.

Do not return your own shorter or more familiar version of that name.


2. BRAND

- Return a brand ONLY when the user explicitly requests or mentions it.
- The brand MUST exactly match one value from CURRENT BRANDS.
- Otherwise return null.
- Never invent a brand.


3. REQUIRED ATTRIBUTES

required_attributes represents HARD product requirements.

Only put something here when the user explicitly requires it.

Examples of explicit requirements include statements such as:

- at least a specific amount of RAM
- a specific storage requirement
- a required port
- a required network capability
- a specific processor
- a specific measurable technical specification

Attribute names MUST exactly match an attribute available for the
selected category.

Never invent attribute names.

Preserve measurable values in a simple technical form.

Examples:

\"16 GB\"
\"512 GB\"
\"5G\"
\"HDMI\"
\"120 Hz\"

Do NOT place vague requirements such as:

\"good\"
\"fast\"
\"powerful\"
\"strong\"
\"long lasting\"
\"high quality\"

inside required_attributes.


4. PREFERRED ATTRIBUTES

preferred_attributes_by_category contains SOFT technical preferences.

Only use an attribute when:

- that exact attribute exists for the relevant category, AND
- the user's request provides enough information to infer a meaningful
  technical preference.

Do NOT invent arbitrary numeric specifications.

For example:

If the customer says:

\"I want a good phone for photography\"

do NOT invent:

\"Rear Camera\": \"48 MP\"

unless the customer actually requested 48 MP.

Photography should instead be represented through use_case and/or
priority_features.

If there is no useful explicit technical preference for a category,
use an empty object for that category.

If the customer explicitly requests a technical requirement but there is
no semantically appropriate attribute for that requirement in the selected
category:

- DO NOT force the requirement into an unrelated existing attribute.
- DO NOT choose the closest-looking attribute merely because it exists.
- Leave that requirement out of required_attributes.
- Preserve it in search_keywords instead.

Attribute meaning must remain semantically correct.

For example, a network capability must not be placed inside an unrelated
charging, battery, display, camera, storage, or other attribute.

5. USE CASE

use_case should briefly describe the customer's primary intended use.

Examples of concepts include:

Gaming
Programming
Business
School
Content Creation
Video Editing
Photography
Music
Fitness
Entertainment
Travel
Home Use
Office Use
Everyday Use

Use the closest concise description.

If no use case is expressed, return null.


6. BUDGET

budget_max:

Extract the maximum numeric budget when the customer provides one.

Examples:

\"under 50000\" -> 50000
\"below 80000\" -> 80000
\"up to 120k\" -> 120000

Otherwise return null.


budget_tier:

Use one of:

\"Budget\"
\"Mid Range\"
\"Premium\"

ONLY when the user's wording clearly indicates a tier.

Do not guess a tier merely from budget_max.

Otherwise return null.


7. PRIORITY FEATURES

priority_features describes important shopping goals that should
influence product ranking.

Use short normalized concepts such as:

\"gaming\"
\"programming\"
\"video_editing\"
\"content_creation\"
\"photography\"
\"business\"
\"school\"
\"travel\"
\"battery_focused\"
\"heavy_compute\"
\"entertainment\"
\"office_use\"

Only include concepts supported by the user's request.

Do not invent unrelated priorities.

When the user's stated use case directly corresponds to one of the supported
priority_features, include that priority feature as well.

Examples:
Photography -> photography
Gaming -> gaming
Programming -> programming
Video Editing -> video_editing
Content Creation -> content_creation
Business -> business
School -> school

8. SEARCH KEYWORDS

search_keywords should contain a small number of useful phrases
representing the customer's request.

They are ranking/search signals, NOT hard requirements.

Avoid duplicating every attribute already represented in
required_attributes.

Keep them concise.


9. MULTI-CATEGORY REQUESTS

Use alternative categories only when the customer's request could
genuinely be satisfied by multiple CURRENT categories.

Do not add alternatives simply to increase the number of results.

For a clearly specific product request, normally return one primary
category and no alternatives.


==================================================
OUTPUT STRUCTURE
==================================================

Return ONLY valid JSON using exactly this structure:

{
  \"category\": {
    \"primary\": null,
    \"alternatives\": []
  },
  \"brand\": null,
  \"use_case\": null,
  \"budget_max\": null,
  \"budget_tier\": null,
  \"required_attributes\": {},
  \"preferred_attributes_by_category\": {},
  \"priority_features\": [],
  \"search_keywords\": []
}

Do not return markdown.
Do not wrap JSON in code blocks.
Do not explain your answer.

==================================================
USER QUERY
==================================================

{$query}
";
    }


    /**
     * Convert the live catalog into prompt-friendly text.
     */
    private function catalogPromptText(array $catalog): string
    {
        $text = "CURRENT CATEGORIES:\n";

        foreach ($catalog['categories'] as $category) {
            $text .= "- {$category}\n";
        }

        $text .= "\nCURRENT BRANDS:\n";

        foreach ($catalog['brands'] as $brand) {
            $text .= "- {$brand}\n";
        }

        $text .= "\nCURRENT ATTRIBUTES BY CATEGORY:\n";

        foreach ($catalog['attributes'] as $category => $attributes) {

            $text .= "\n{$category}:\n";

            if (empty($attributes)) {
                $text .= "- No attributes currently available\n";
                continue;
            }

            foreach ($attributes as $attribute) {
                $text .= "- {$attribute}\n";
            }
        }

        return $text;
    }


    /**
     * Validate AI output against the current database vocabulary.
     *
     * This does NOT contain category mappings or aliases.
     */
    private function validateIntent(
        array $intent,
        array $catalog
    ): array {

        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | BASIC STRUCTURE
        |--------------------------------------------------------------------------
        */

        if (
            !isset($intent['category']) ||
            !is_array($intent['category'])
        ) {
            $errors[] = 'category must be an object containing primary and alternatives.';
        }

        $primary = $intent['category']['primary'] ?? null;

        $alternatives = $intent['category']['alternatives'] ?? [];

        if (!is_array($alternatives)) {
            $errors[] = 'category.alternatives must be an array.';
            $alternatives = [];
        }


        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        if (
            $primary !== null &&
            !in_array($primary, $catalog['categories'], true)
        ) {
            $errors[] =
                "Invalid primary category '{$primary}'. It must exactly match a current category.";
        }

        foreach ($alternatives as $category) {

            if (!in_array($category, $catalog['categories'], true)) {

                $errors[] =
                    "Invalid alternative category '{$category}'. It must exactly match a current category.";
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BRAND
        |--------------------------------------------------------------------------
        */

        $brand = $intent['brand'] ?? null;

        if (
            $brand !== null &&
            !in_array($brand, $catalog['brands'], true)
        ) {
            $errors[] =
                "Invalid brand '{$brand}'. It must exactly match a current brand.";
        }


        /*
        |--------------------------------------------------------------------------
        | REQUIRED ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        $requiredAttributes =
            $intent['required_attributes'] ?? [];

        if (!is_array($requiredAttributes)) {

            $errors[] =
                'required_attributes must be an object.';

        } elseif ($primary) {

            $availableAttributes =
                $catalog['attributes'][$primary] ?? [];

            foreach ($requiredAttributes as $name => $value) {

                if (!in_array($name, $availableAttributes, true)) {

                    $errors[] =
                        "Invalid required attribute '{$name}' for category '{$primary}'.";
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | PREFERRED ATTRIBUTES
        |--------------------------------------------------------------------------
        */

        $preferences =
            $intent['preferred_attributes_by_category'] ?? [];

        if (!is_array($preferences)) {

            $errors[] =
                'preferred_attributes_by_category must be an object.';

        } else {

            $allowedCategories = array_values(
                array_filter(
                    array_merge(
                        [$primary],
                        $alternatives
                    )
                )
            );

            foreach ($preferences as $category => $attributes) {

                if (!in_array($category, $allowedCategories, true)) {

                    $errors[] =
                        "Preferred attributes contain unrelated category '{$category}'.";

                    continue;
                }

                if (!is_array($attributes)) {

                    $errors[] =
                        "Preferred attributes for '{$category}' must be an object.";

                    continue;
                }

                $availableAttributes =
                    $catalog['attributes'][$category] ?? [];

                foreach ($attributes as $name => $value) {

                    if (!in_array($name, $availableAttributes, true)) {

                        $errors[] =
                            "Invalid preferred attribute '{$name}' for category '{$category}'.";
                    }
                }
            }
        }


        /*
        |--------------------------------------------------------------------------
        | BUDGET
        |--------------------------------------------------------------------------
        */

        if (
            isset($intent['budget_max']) &&
            $intent['budget_max'] !== null &&
            !is_numeric($intent['budget_max'])
        ) {
            $errors[] =
                'budget_max must be numeric or null.';
        }


        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }


    /**
     * Normalize the final intent into one predictable schema.
     */
    private function normalizeIntent(array $intent): array
    {
        return [
            'category' => [
                'primary' =>
                    $intent['category']['primary'] ?? null,

                'alternatives' =>
                    array_values(
                        array_unique(
                            $intent['category']['alternatives'] ?? []
                        )
                    ),
            ],

            'brand' =>
                $intent['brand'] ?? null,

            'use_case' =>
                $intent['use_case'] ?? null,

            'budget_max' =>
                isset($intent['budget_max']) &&
                is_numeric($intent['budget_max'])
                    ? (float) $intent['budget_max']
                    : null,

            'budget_tier' =>
                $intent['budget_tier'] ?? null,

            'required_attributes' =>
                is_array($intent['required_attributes'] ?? null)
                    ? $intent['required_attributes']
                    : [],

            'preferred_attributes_by_category' =>
                is_array(
                    $intent['preferred_attributes_by_category'] ?? null
                )
                    ? $intent['preferred_attributes_by_category']
                    : [],

            'priority_features' =>
                is_array($intent['priority_features'] ?? null)
                    ? array_values(
                        array_unique($intent['priority_features'])
                    )
                    : [],

            'search_keywords' =>
                is_array($intent['search_keywords'] ?? null)
                    ? array_values(
                        array_unique($intent['search_keywords'])
                    )
                    : [],
        ];
    }


    /**
     * Send a prompt to Replicate and return decoded JSON.
     */
    private function callModel(string $prompt): ?array
    {
        try {

            $response = Http::withToken(
                config('services.replicate.token')
            )
                ->timeout(30)
                ->post(self::MODEL_URL, [
                    'input' => [
                        'prompt' => $prompt,

                        'system_prompt' =>
                            'You are SmartCart AI. Return ONLY valid JSON. Never invent catalog values.',

                        'max_tokens' => 500,
                        'temperature' => 0.1,
                    ],
                ]);

            if (!$response->successful()) {

                Log::error('SmartCart AI intent request failed.', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return null;
            }

            $prediction = $response->json();

            $getUrl = $prediction['urls']['get'] ?? null;
            $status = $prediction['status'] ?? null;

            if (!$getUrl) {

                Log::error(
                    'SmartCart AI intent response did not contain polling URL.'
                );

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | POLL PREDICTION
            |--------------------------------------------------------------------------
            */

            for ($attempt = 0; $attempt < 15; $attempt++) {

                if ($status === 'succeeded') {
                    return $this->decodePrediction($prediction);
                }

                if (
                    $status === 'failed' ||
                    $status === 'canceled'
                ) {

                    Log::error(
                        'SmartCart AI intent prediction failed.',
                        [
                            'status' => $status,
                            'error' => $prediction['error'] ?? null,
                        ]
                    );

                    return null;
                }

                sleep(1);

                $pollResponse = Http::withToken(
                    config('services.replicate.token')
                )
                    ->timeout(15)
                    ->get($getUrl);

                if (!$pollResponse->successful()) {

                    Log::error(
                        'SmartCart AI intent polling failed.',
                        [
                            'status' => $pollResponse->status(),
                        ]
                    );

                    return null;
                }

                $prediction = $pollResponse->json();
                $status = $prediction['status'] ?? null;
            }

            Log::warning(
                'SmartCart AI intent prediction timed out.'
            );

            return null;

        } catch (\Throwable $exception) {

            Log::error(
                'SmartCart AI intent exception.',
                [
                    'message' => $exception->getMessage(),
                ]
            );

            return null;
        }
    }


    /**
     * Decode Replicate prediction output.
     */
    private function decodePrediction(array $prediction): ?array
    {
        $output = $prediction['output'] ?? null;

        $text = is_array($output)
            ? implode('', $output)
            : $output;

        if (!$text) {
            return null;
        }

        $text = trim($text);

        /*
        |--------------------------------------------------------------------------
        | Defensive cleanup
        |--------------------------------------------------------------------------
        |
        | The prompt tells the model not to use markdown fences, but removing
        | them here protects us if it occasionally does.
        |
        */

        $text = preg_replace(
            '/^```(?:json)?\s*|\s*```$/i',
            '',
            $text
        );

        $decoded = json_decode(
            trim($text),
            true
        );

        if (!is_array($decoded)) {

            Log::warning(
                'SmartCart AI returned invalid JSON.',
                [
                    'output' => $text,
                ]
            );

            return null;
        }

        return $decoded;
    }


    /**
     * Safe intent returned when AI extraction cannot be completed.
     */
    private function emptyIntent(): array
    {
        return [
            'category' => [
                'primary' => null,
                'alternatives' => [],
            ],
            'brand' => null,
            'use_case' => null,
            'budget_max' => null,
            'budget_tier' => null,
            'required_attributes' => [],
            'preferred_attributes_by_category' => [],
            'priority_features' => [],
            'search_keywords' => [],
        ];
    }
}