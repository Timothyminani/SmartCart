<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiExplanationService
{
    public function generate(
        string $query,
        array $intent,
        $products
    ): array {
        /*
        |--------------------------------------------------------------------------
        | NO MATCHES
        |--------------------------------------------------------------------------
        |
        | Do not call the AI when there are no products to explain.
        |
        */

        if ($products->isEmpty()) {
            return [
                'ai_explanation' => '',
                'refinement_suggestions' => $this->buildNoMatchSuggestions($intent),
            ];
        }


            sleep(12);
        /*
        |--------------------------------------------------------------------------
        | PRODUCT SUMMARY
        |--------------------------------------------------------------------------
        |
        | Only send real catalog data.
        | No fabricated product signals.
        |
        */

        $productSummary = $products
            ->map(function ($product) {
                return [
                    'name' => $product->name,

                    'price' => $this->effectivePrice($product),

                    'brand' => $product->brand->name ?? null,

                    'category' => $product->category->name ?? null,

                    'ai_score' => $product->ai_score ?? null,

                    'attributes' => $product->attributes
                        ->map(function ($attribute) {
                            return [
                                'name' => $attribute->attribute_name,
                                'value' => $attribute->attribute_value,
                            ];
                        })
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();

        /*
        |--------------------------------------------------------------------------
        | PROMPT
        |--------------------------------------------------------------------------
        */

        $prompt = "
You are SmartCart AI, an ecommerce shopping assistant.

Your job is to explain WHY the supplied products are relevant to the customer's request.

You are NOT searching the catalog.
You are NOT allowed to invent products.
You are NOT allowed to invent specifications.

The PRODUCT DATA below is the only product information you may use.

==================================================
CUSTOMER QUERY
==================================================

{$query}

==================================================
INTERPRETED CUSTOMER INTENT
==================================================

" . json_encode(
            $intent,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ) . "

==================================================
MATCHING PRODUCTS
==================================================

" . json_encode(
            $productSummary,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ) . "

==================================================
CORE RULES
==================================================

1. Use ONLY the supplied product data.

2. Never invent:
- specifications
- battery life
- performance claims
- camera quality
- display quality
- materials
- warranty information
- benchmarks
- software compatibility
- ports
- features
- product capabilities

unless they are explicitly supported by the supplied product data.

3. Do not assume that a product has a feature simply because:
- its name sounds premium
- its model family usually has that feature
- similar products commonly have that feature

4. Respect the customer's hard requirements and budget.

5. Do not claim that a product satisfies a requirement unless the supplied data supports that claim.

6. The products have already been filtered and ranked by SmartCart.
Your task is to explain the strongest matches and meaningful tradeoffs.

7. If there is only one product, do not create fake comparisons.

8. If product data is insufficient to compare something, simply leave that comparison out.

9. PERFORMANCE COMPARISON RULE

Do not claim that one processor, GPU, camera, battery, display, or product
performs better than another unless the supplied product data directly
supports that comparison.

Do not infer benchmark performance from model names alone.

You may describe factual differences such as:
- processor model
- core count
- RAM amount
- storage amount
- battery specification
- display specification
- price
- weight

But do not convert those differences into unsupported claims such as:
- faster
- more powerful
- better performance
- better camera
- better display
- better battery life

unless the supplied product data directly supports that conclusion.

==================================================
AI EXPLANATION
==================================================

Write a concise, useful shopping explanation.

Use markdown inside ai_explanation.

Prefer this structure when appropriate:

## Best Matches

Explain the strongest product choices.

For each recommended product:
- name the actual product
- explain why it fits the customer's request
- mention relevant strengths supported by the data
- mention meaningful tradeoffs when supported by the data

Example style:

- **Best Overall — Product Name:** concise reason.
- **Best Value — Product Name:** concise reason.
- **Alternative — Product Name:** concise reason.

Do NOT force these exact labels if they do not fit the available products.

==================================================
KEY DIFFERENCES
==================================================

When there are multiple products, briefly explain the most useful differences.

Only compare information actually present in the product data.

Possible differences may include:
- price
- RAM
- storage
- processor
- graphics
- display
- battery
- network
- camera
- ports
- other supplied attributes

Do not include irrelevant categories of comparison.

==================================================
BUYING ADVICE
==================================================

Give a short final recommendation based on the customer's stated needs.

Explain which product is the strongest fit and why.

Do not tell the customer to buy a product based on unsupported claims.

==================================================
QUICK SPECS GUIDE
==================================================

Include a small buying guide ONLY when it adds useful context.

The guide should explain what specifications matter for the customer's use case.

This guide is general shopping guidance and MUST NOT falsely attribute those specifications to the supplied products.

Keep it short.

A markdown table may be used when useful.

==================================================
REFINEMENT SUGGESTIONS
==================================================

Return between 0 and 5 short clickable search refinements.

They should:
- relate directly to the customer's current search
- help narrow or adjust the search
- sound like natural searches
- not be conversational questions
- not claim unsupported product facts

Examples of style:

- Lower budget alternatives
- More storage options
- Lightweight options for travel
- Premium photography phones
- Longer battery life options
- Gaming laptops with dedicated graphics

Do not make every suggestion laptop-specific.
Adapt them to the current category and customer intent.

==================================================
OUTPUT FORMAT
==================================================

Return ONLY valid JSON in exactly this structure:

{
  \"ai_explanation\": \"markdown explanation\",
  \"refinement_suggestions\": [
    \"suggestion 1\",
    \"suggestion 2\",
    \"suggestion 3\"
  ]
}

Do not return HTML.
Do not wrap the JSON in markdown code fences.
Do not include text before or after the JSON.
";

        /*
        |--------------------------------------------------------------------------
        | CALL MODEL
        |--------------------------------------------------------------------------
        */

        try {
            $response = Http::withToken(
                config('services.replicate.token')
            )
                ->timeout(30)
                ->post(
                    'https://api.replicate.com/v1/models/openai/gpt-4o-mini/predictions',
                    [
                        'input' => [
                            'prompt' => $prompt,

                            'system_prompt' =>
                                'You are SmartCart AI. Use only supplied catalog data. Never invent product facts. Return only valid JSON with ai_explanation and refinement_suggestions.',

                            'max_tokens' => 600,

                            'temperature' => 0.2,
                        ],
                    ]
                );

            if (!$response->successful()) {
                Log::warning(
                    'SmartCart AI explanation request failed.',
                    [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]
                );

                return $this->fallbackResponse();
            }

            $prediction = $response->json();

            $getUrl = $prediction['urls']['get'] ?? null;

            if (!$getUrl) {
                Log::warning(
                    'SmartCart AI explanation prediction missing polling URL.',
                    [
                        'prediction' => $prediction,
                    ]
                );

                return $this->fallbackResponse();
            }







            /*
            |--------------------------------------------------------------------------
            | POLL
            |--------------------------------------------------------------------------
            */

            for ($attempt = 0; $attempt < 15; $attempt++) {

                sleep(1);

                $pollResponse = Http::withToken(
                    config('services.replicate.token')
                )
                    ->timeout(30)
                    ->get($getUrl);

                if (!$pollResponse->successful()) {
                    Log::warning(
                        'SmartCart AI explanation polling failed.',
                        [
                            'status' => $pollResponse->status(),
                            'body' => $pollResponse->body(),
                        ]
                    );

                    continue;
                }

                $result = $pollResponse->json();

                $status = $result['status'] ?? null;

                if ($status === 'succeeded') {
                    return $this->parseResult(
                        $result['output'] ?? null
                    );
                }

                if (
                    $status === 'failed' ||
                    $status === 'canceled'
                ) {
                    Log::warning(
                        'SmartCart AI explanation prediction did not succeed.',
                        [
                            'status' => $status,
                            'error' => $result['error'] ?? null,
                        ]
                    );

                    return $this->fallbackResponse();
                }
            }

            Log::warning(
                'SmartCart AI explanation timed out while polling.'
            );

            return $this->fallbackResponse();

        } catch (\Throwable $e) {

            Log::error(
                'SmartCart AI explanation exception.',
                [
                    'message' => $e->getMessage(),
                ]
            );

            return $this->fallbackResponse();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | PARSE MODEL OUTPUT
    |--------------------------------------------------------------------------
    */

    private function parseResult($output): array
    {
        $text = is_array($output)
            ? implode('', $output)
            : (string) $output;

        $text = trim($text);

        if ($text === '') {
            return $this->fallbackResponse();
        }

        /*
         * Defensive cleanup if the model unexpectedly adds code fences.
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
                'SmartCart AI explanation returned invalid JSON.',
                [
                    'output' => $text,
                ]
            );

            return $this->fallbackResponse();
        }

        $explanation =
            $decoded['ai_explanation'] ?? '';

        $suggestions =
            $decoded['refinement_suggestions'] ?? [];

        if (!is_string($explanation)) {
            $explanation = '';
        }

        if (!is_array($suggestions)) {
            $suggestions = [];
        }

        $suggestions = collect($suggestions)
            ->filter(fn ($suggestion) => is_string($suggestion))
            ->map(fn ($suggestion) => trim($suggestion))
            ->filter()
            ->unique()
            ->take(5)
            ->values()
            ->toArray();

        return [
            'ai_explanation' => trim($explanation),
            'refinement_suggestions' => $suggestions,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | NO MATCH SUGGESTIONS
    |--------------------------------------------------------------------------
    */

    private function buildNoMatchSuggestions(
        array $intent
    ): array {
        $suggestions = [];

        $category =
            $intent['category']['primary'] ?? null;

        if (!empty($intent['budget_max'])) {
            $suggestions[] =
                'Show options with a higher budget';
        }

        if (!empty($intent['brand'])) {
            $suggestions[] =
                'Similar options from other brands';
        }

        if (!empty($intent['required_attributes'])) {
            $suggestions[] =
                'Show options with fewer specification requirements';
        }

        if ($category) {
            $suggestions[] =
                "Browse other {$category} options";
        }

        return array_slice(
            array_values(
                array_unique($suggestions)
            ),
            0,
            5
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EFFECTIVE PRICE
    |--------------------------------------------------------------------------
    */

    private function effectivePrice(
        $product
    ): float {
        $salePrice =
            (float) ($product->sale_price ?? 0);

        if ($salePrice > 0) {
            return $salePrice;
        }

        return (float) (
            $product->price ?? 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | SAFE FALLBACK
    |--------------------------------------------------------------------------
    */

    private function fallbackResponse(): array
    {
        return [
            'ai_explanation' => '',
            'refinement_suggestions' => [],
        ];
    }
}