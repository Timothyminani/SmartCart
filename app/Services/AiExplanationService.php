<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiExplanationService
{
    public function generate(string $query, array $intent, $products): array
    {
        
        // =========================
        // ENRICH PRODUCT DATA
        // =========================
        $productSummary = $products->map(function ($p) {

            return [
                'name' => $p->name,
                'price' => $p->sale_price,
                'brand' => $p->brand->name ?? null,
                'category' => $p->category->name ?? null,

                // IMPORTANT: ATTRIBUTES FOR AI REASONING
                'attributes' => $p->attributes->map(function ($a) {
                    return [
                        'name' => $a->attribute_name,
                        'value' => $a->attribute_value,
                    ];
                })->toArray(),

                // EXTRA SIGNALS (VERY USEFUL FOR AI)
                'spec_signals' => [
                    'has_rtx' => str_contains(strtolower($p->name), 'rtx'),
                    'has_high_ram' => str_contains(strtolower($p->name), '16gb') ||
                                      str_contains(strtolower($p->name), '32gb'),
                    'has_ssd' => true, // assume SSD unless you track it
                    'is_premium' => str_contains(strtolower($p->name), 'pro') ||
                                    str_contains(strtolower($p->name), 'xps') ||
                                    str_contains(strtolower($p->name), 'macbook'),
                ]
            ];
        })->toArray();

        // =========================
        // PROMPT
        // =========================
        $prompt = "
You are SmartCart AI.

You are an elite ecommerce shopping assistant similar to Alibaba AI, Amazon Rufus, and Perplexity Shopping.

Your job is to help users choose the BEST products based on their needs.

==================================================
USER QUERY
==================================================
{$query}

==================================================
USER INTENT
==================================================
" . json_encode($intent, JSON_PRETTY_PRINT) . "

==================================================
PRODUCTS DATA
==================================================
" . json_encode($productSummary, JSON_PRETTY_PRINT) . "

==================================================
RESPONSE INSTRUCTIONS
==================================================

Generate a PROFESSIONAL ecommerce AI response.

The response MUST feel premium, intelligent, and highly structured.

Use this exact structure:

1. Short overview paragraph
2. Top recommendations section
3. Key differences section
4. Buying advice section
5. Quick Specs Guide section

==================================================
MARKDOWN FORMATTING RULES
==================================================

Format the response using PROPER MARKDOWN.

Use:

## Section Titles

Example:
## Top Recommendations

Use bullet points for recommendations:

- **Best Overall:** explanation here
- **Best Budget Option:** explanation here

Use bold labels for important attributes:

- **Performance:** excellent for gaming
- **Battery:** lasts up to 10 hours

Leave ONE empty line between sections.

DO NOT return HTML.

==================================================
IMPORTANT RULES
==================================================

- Sound like a premium AI shopping assistant
- Be conversational and intelligent
- DO NOT repeat raw attributes
- DO NOT hallucinate specifications
- Keep explanations concise but informative
- Use headings
- Use bullet points where appropriate
- Mention tradeoffs naturally
- Make the response feel modern and AI-native


IMPORTANT MARKDOWN RULES:
- Use # for main headings
- Use ## for section headings
- Use ### for subsection headings
- Use bullet points using -
- Use bold text using **
- Add spacing between sections
- DO NOT wrap response in code blocks
- DO NOT return HTML
- Format output like a modern AI assistant response

Example format:

## AI Mode Overview

For content creators, laptops with strong GPUs, high RAM, and color-accurate displays deliver the best editing experience.

2. Top Recommendations section

The \"Top Recommendations\" section title should dynamically adapt to the user's intent.

Examples:
- Top Recommendations for Adobe Illustrator
- Top Recommendations for Gaming

The content under this section MUST remain in bullet format:

- **Best Overall:** explanation here
- **Best Budget Option:** explanation here
- **Best Premium Option:** explanation here

Each recommendation must include:
- a clear label (Best Overall, Budget, Premium, etc.)
- a short

## Key Differences

- **Performance:** MacBook Pro leads in rendering efficiency.

- **Battery:** MacBook lasts longer than most Windows alternatives.

- **Portability:** Zenbook is lighter and easier to carry.

## Buying Advice

- **For Professionals:** Choose MacBook Pro.

- **For Students:** Zenbook offers better value.

##Quick Specs Guide for the User's Use Case

Create a small buying guide showing the MOST IMPORTANT specifications the user should pay attention to when choosing products.

The section title MUST dynamically adapt to the user's needs.

Examples:
- Quick Specs Guide for Adobe Illustrator
- Quick Specs Guide for Gaming

Use a markdown table WHEN appropriate.

The table should help the user understand:
- minimum recommended specs
- ideal specs for professionals
- what features matter most

Examples of things to compare:
- RAM
- storage
- display
- GPU
- battery
- processor
- refresh rate
- camera quality
- cooling
- portability

Only include specifications relevant to the user's query.

- Use markdown tables when useful
- Tables should look clean and readable
- Keep tables concise
";

$prompt .= "

==================================================
FINAL RESPONSE FORMAT
==================================================

Return your response STRICTLY in valid JSON format.

Use this exact structure:

{
  \"ai_explanation\": \"markdown formatted explanation here\",
  \"refinement_suggestions\": [
    \"suggestion 1\",
    \"suggestion 2\",
    \"suggestion 3\"
  ]
}

IMPORTANT RULES:

- ai_explanation MUST contain markdown

- refinement_suggestions are NOT conversational questions

- refinement_suggestions MUST behave like:
  - search refinements
  - shopping filters
  - buying constraints
  - product narrowing suggestions

GOOD EXAMPLES:
- Lightweight laptops for travel
- RTX laptops for video editing
- Budget gaming laptops under 60k
- OLED display creator laptops
- Quiet laptops for office work
- Long battery ultrabooks
- Premium laptops for developers

BAD EXAMPLES:
- What is your budget?
- Do you have a preferred brand?
- What software will you use?
- Budget: under 50k
- RAM: 16GB
- Display: Full HD
- Battery: 5 hours

- Suggestions should be short
- Suggestions should feel clickable
- Suggestions should NOT sound like a conversation
- Avoid sounding like filters or forms
- Suggestions should feel like something users would actually search for
- They should be not more than 5 suggestions
- The questions should be based on common shopping constraints and refinements users typically consider when shopping for laptops and also should be somehow related to the user's query.
- DO NOT return HTML
- DO NOT wrap JSON in code blocks
- Return ONLY valid JSON

If you do not return valid JSON, the system will reject your response.
";

        // =========================
        // GPT-4o MINI (Replicate)
        // =========================
       
     $response = Http::withToken(config('services.replicate.token'))
    ->post('https://api.replicate.com/v1/models/openai/gpt-4o-mini/predictions', [
        'input' => [
            'prompt' => $prompt,
            'system_prompt' => 'You are SmartCart AI. Always follow the user prompt exactly. Return ONLY valid JSON with ai_explanation (markdown) and refinement_suggestions array. No exceptions.',
            'max_tokens' => 400,
            'temperature' => 0.4,
        ]
    ]);

$prediction = $response->json();

$getUrl = $prediction['urls']['get'] ?? null;

if (!$getUrl) {
    return 'Unable to generate AI explanation.';
}

// Poll Replicate until prediction completes
for ($i = 0; $i < 15; $i++) {

    

    $pollResponse = Http::withToken(config('services.replicate.token'))
        ->get($getUrl);

    $result = $pollResponse->json();

    if (($result['status'] ?? null) === 'succeeded') {

    $output = $result['output'] ?? null;

    $text = is_array($output)
        ? implode('', $output)
        : $output;

    $decoded = json_decode(trim($text ?? ''), true);

    if (!$decoded) {
        return [
            'ai_explanation' => $text ?? '',
            'refinement_suggestions' => []
        ];
    }

    return [
        'ai_explanation' => $decoded['ai_explanation'] ?? '',
        'refinement_suggestions' => $decoded['refinement_suggestions'] ?? []
    ];
}




    if (($result['status'] ?? null) === 'failed') {

     return [
    'ai_explanation' => 'AI explanation generation failed.',
    'refinement_suggestions' => []
];
    }

        sleep(2); // wait before next poll
}


return [
    'ai_explanation' => 'AI explanation is taking longer than expected.',
    'refinement_suggestions' => []
];
    }
}