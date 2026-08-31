<?php

namespace App\Jobs;

use App\Models\AiComparison;
use App\Models\Product;
use App\Services\Ai\ReplicateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateAiComparison implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $comparisonId;

    public function __construct($comparisonId)
    {
        $this->comparisonId = $comparisonId;
    }

    public function handle(ReplicateService $ai)
    {
        $comparison = AiComparison::find($this->comparisonId);

        if (!$comparison) {
            return;
        }

        $comparison->update([
            'status' => 'processing',
            'error' => null,
        ]);

        try {
            /*
            |--------------------------------------------------------------------------
            | FETCH PRODUCTS
            |--------------------------------------------------------------------------
            */

            $products = Product::with([
                'attributes',
                'brand',
                'category',
            ])
                ->whereIn('id', $comparison->product_ids)
                ->get();

            /*
            |--------------------------------------------------------------------------
            | VALIDATE PRODUCTS
            |--------------------------------------------------------------------------
            |
            | The comparison record should always contain exactly two products.
            | We check again here because queued jobs may execute later.
            |
            */

            if ($products->count() !== 2) {
                throw new \Exception(
                    'Exactly two valid products are required for comparison.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | BUILD PROMPT
            |--------------------------------------------------------------------------
            */

            $prompt = $this->buildPrompt($products);

            /*
            |--------------------------------------------------------------------------
            | GENERATE AI COMPARISON
            |--------------------------------------------------------------------------
            */

            $predictionId = $ai->generate($prompt);

            $result = $ai->waitForResult($predictionId);

            /*
            |--------------------------------------------------------------------------
            | SAVE RESULT
            |--------------------------------------------------------------------------
            */

            $comparison->update([
                'result' => $result,
                'status' => 'completed',
                'error' => null,
            ]);

        } catch (\Throwable $e) {
            $comparison->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Build the comparison prompt using only real catalog data.
     */
    private function buildPrompt($products): string
    {
        $productText = '';

        foreach ($products as $index => $product) {
            $price = $this->effectivePrice($product);

            $productText .=
                'Product ' . ($index + 1) . ":\n";

            $productText .=
                "Name: {$product->name}\n";

            $productText .=
                "Price: KES " . number_format($price, 2) . "\n";

            $productText .=
                "Brand: " . ($product->brand->name ?? 'Not provided') . "\n";

            $productText .=
                "Category: " . ($product->category->name ?? 'Not provided') . "\n";

            $productText .= "Attributes:\n";

            foreach ($product->attributes as $attribute) {
                $productText .=
                    "- {$attribute->attribute_name}: {$attribute->attribute_value}\n";
            }

            $productText .= "\n";
        }

        return <<<PROMPT
You are SmartCart AI, an ecommerce product comparison assistant.

Your job is to compare the TWO supplied products using ONLY the product
information provided below.

You are NOT searching for products.
You do NOT have permission to add missing product information.
The SmartCart catalog data below is the source of truth.

=====================================================
PRODUCT DATA
=====================================================

{$productText}

=====================================================
CORE RULES
=====================================================

1. Use ONLY the supplied product data.

2. Never invent or assume:
- specifications
- features
- battery life
- benchmark results
- camera quality
- display quality
- build quality
- durability
- software compatibility
- ports
- upgradeability
- performance results
- product capabilities
- warranty information

unless that information is explicitly supported by the supplied data.

3. Do not use outside knowledge about these product models.

For example, do not assume something about a processor, phone, laptop,
television, or other product simply because you recognize its model name.

4. You may describe factual differences that are directly visible in the data.

Examples:
- one product has more RAM
- one product has more storage
- one product has a larger battery capacity
- one product has a larger display
- one product has a different processor model
- one product has different graphics
- one product costs less
- one product provides a listed feature that the other does not

5. Do NOT convert specifications into unsupported performance claims.

For example:

Allowed:
"Product A has 16 GB RAM while Product B has 8 GB RAM."

Not allowed:
"Product A is twice as fast."

Allowed:
"Product A lists a 5000 mAh battery while Product B lists 4500 mAh."

Not allowed:
"Product A lasts much longer."

Allowed:
"Product A uses Processor X while Product B uses Processor Y."

Not allowed:
"Processor X is faster."

unless the supplied product data directly supports that conclusion.

6. If the supplied data is insufficient to determine a winner for a
particular area, say so briefly or leave that comparison out.

7. Do not manufacture numerical ratings or scores.

Do NOT create arbitrary ratings such as:
- 92/100 performance
- 8.5/10 battery
- 95% display quality

The catalog does not provide those scores.

8. A more expensive product is NOT automatically better.

Use price together with the supplied specifications when discussing value.

9. Mention meaningful tradeoffs honestly.

10. Avoid generic marketing language.

=====================================================
CATEGORY ADAPTATION
=====================================================

Adapt the comparison dynamically to the actual products and the attributes
that are available.

Do NOT use a fixed comparison template for every product category.

For example:

For laptops, relevant supplied attributes might include:
- processor
- RAM
- storage
- graphics
- display
- battery
- ports
- operating system

For phones, relevant supplied attributes might include:
- processor
- RAM
- storage
- display
- cameras
- battery
- network
- operating system

For TVs, relevant supplied attributes might include:
- display size
- resolution
- refresh rate
- panel/display technology
- connectivity

These are examples only.

Use ONLY comparison areas actually supported by the supplied product data.

=====================================================
OUTPUT FORMAT
=====================================================

Return ONLY clean Markdown.

Do NOT return JSON.
Do NOT use markdown code fences.
Do NOT include text before or after the comparison.

Use the following structure:

# Overall Verdict

Give a concise overall assessment.

If one product has a clearly stronger overall combination of supplied
specifications and price, explain why using those facts.

If the supplied data does not support declaring one product the absolute
winner, say that the better choice depends on the buyer's priorities and
explain those priorities.

---

# Specification Comparison

Create a Markdown table using the most useful attributes available for
these products.

Example structure:

| Specification | Product A | Product B |
|---|---|---|
| Price | ... | ... |
| RAM | ... | ... |
| Storage | ... | ... |

Use the ACTUAL product names as the table column headings.

Do not add specifications that are not present in the supplied data.

---

# Strengths

Create a subsection for EACH product.

## Actual Product Name

List strengths that can be directly supported by its supplied
specifications or price.

---

# Weaknesses

Create a subsection for EACH product.

## Actual Product Name

Mention meaningful disadvantages relative to the other product ONLY when
the supplied data supports them.

Do not invent weaknesses.

If there is not enough information to identify a meaningful weakness,
say that no clear weakness can be determined from the supplied data.

---

# Best For

Explain which types of buyers may prefer each product based on the
supplied differences.

Keep these recommendations conservative.

Do not claim suitability for specialized tasks unless the supplied
specifications reasonably support the recommendation.

---

# Key Differences

Explain the most important factual differences between the products.

Focus on differences that could realistically affect a buying decision.

---

# Buying Advice

Finish with practical guidance.

Use a structure similar to:

Choose **Product A** if:
- factual reason based on supplied data
- factual reason based on supplied data

Choose **Product B** if:
- factual reason based on supplied data
- factual reason based on supplied data

Do not introduce new product facts in this section.

=====================================================
FINAL CHECK
=====================================================

Before returning the comparison, verify that:

- every product fact came from PRODUCT DATA
- every specification mentioned exists in PRODUCT DATA
- no numerical rating was invented
- no benchmark result was invented
- no unsupported performance claim was made
- no unsupported battery, camera, display, build-quality, or durability
  claim was made
- both products were compared fairly
- the output contains only clean Markdown
PROMPT;
    }

    /**
     * Return the actual selling price.
     */
    private function effectivePrice($product): float
    {
        if (
            $product->sale_price !== null &&
            (float) $product->sale_price > 0
        ) {
            return (float) $product->sale_price;
        }

        return (float) $product->price;
    }
}