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
        // FIND COMPARISON
        $comparison = AiComparison::find($this->comparisonId);

        if (!$comparison) {
            return;
        }

        // UPDATE STATUS
        $comparison->update([
            'status' => 'processing'
        ]);

        try {

            // FETCH PRODUCTS
            $products = Product::with([
                'attributes',
                'brand',
                'category'
            ])
            ->whereIn('id', $comparison->product_ids)
            ->get();

            // BUILD PROMPT
            $prompt = $this->buildPrompt($products);

            // GENERATE AI
            $predictionId = $ai->generate($prompt);

           

            // GET RESULT
            $result = $ai->waitForResult($predictionId);

            // SAVE RESULT
            $comparison->update([
                'result' => $result,
                'status' => 'completed'
            ]);

        } catch (\Exception $e) {

            $comparison->update([
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);

        }
    }


private function buildPrompt($products)
{
    $productText = '';

    foreach ($products as $index => $product) {

        $productText .= "
Product " . ($index + 1) . ":
Name: {$product->name}
Price: {$product->sale_price}
Brand: {$product->brand->name}
Category: {$product->category->name}

Attributes:
";

        foreach ($product->attributes as $attribute) {

            $productText .= "- {$attribute->attribute_name}: {$attribute->attribute_value}\n";
        }

        $productText .= "\n";
    }

  

return "
You are an advanced ecommerce AI comparison engine.

Your job is to compare products intelligently based on:

- specifications
- pricing
- usability
- quality
- real-world experience
- value for money

You must behave like a professional ecommerce product analyst.

=====================================================
PRODUCTS
=====================================================

$productText

=====================================================
IMPORTANT RULES
=====================================================

- Return ONLY clean Markdown
- Do NOT return JSON
- Do NOT return code blocks
- Do NOT return explanations outside markdown
- Be analytical and realistic
- Never invent specifications
- Base analysis ONLY on provided attributes
- Mention both strengths and weaknesses honestly
- Mention tradeoffs clearly
- Avoid generic marketing language

=====================================================
IMPORTANT CATEGORY RULE
=====================================================

The comparison MUST adapt dynamically depending on the product category.

Examples:

- Laptops:
  performance, display, battery, portability

- Washing machines:
  capacity, efficiency, wash quality, noise

- Printers:
  print quality, print speed, ink efficiency

- TVs:
  resolution, brightness, refresh rate, smart features

- Furniture:
  comfort, materials, durability, size

- Phones:
  camera, battery, performance, display

DO NOT force irrelevant comparison categories.

=====================================================
MARKDOWN STRUCTURE
=====================================================

Use markdown sections EXACTLY like this:

# Overall Verdict

Explain:
- which product wins overall
- why it wins
- important tradeoffs

Example:

The **MacBook Air M3** is the better overall laptop because it offers excellent battery life, premium build quality, and strong real-world performance.

The **ASUS TUF Gaming A15** is better for gaming and GPU-intensive workloads.

---

#Score Comparison

Create a markdown table.

Example:

| Category | Product A | Product B |
|---|---|---|
| Performance | 89 | 93 |
| Battery | 97 | 74 |
| Display | 90 | 86 |
| Value | 84 | 91 |

Categories MUST adapt dynamically depending on the product type.

---

#Strengths

Create strengths section for EACH product.

Example:

## Product Name
- Strength 1
- Strength 2
- Strength 3

---

# Weaknesses

Create weaknesses section for EACH product.

Example:

## Product Name
- Weakness 1
- Weakness 2

---

# Best For

Recommend products for different users.

Examples:
- Students
- Professionals
- Gamers
- Families
- Budget buyers
- Travelers

Use ONLY categories that make sense for the product type.

Example:

## Best For Students

**Product Name**

Reason why it fits students.

---

# Key Differences

Mention major real-world differences.

Examples:
- Better battery
- Better gaming performance
- Better efficiency
- Better build quality
- Better value

Explain WHY those differences matter.

---

# Buying Advice

Provide practical buying advice.

Example:

Choose Product A if:
- portability matters most
- battery life is important
- you travel often

Choose Product B if:
- gaming matters most
- maximum performance matters
- upgradeability matters

=====================================================
SCORING RULES
=====================================================

Scores must:
- be realistic
- reflect actual differences
- consider specifications
- consider pricing
- consider usability
- consider value for money

Do NOT give nearly identical scores unless products are genuinely similar.

=====================================================
VALUE FOR MONEY RULE
=====================================================

A more expensive product is NOT automatically better.

Determine whether:
- extra features justify the extra cost
- performance improvements are meaningful
- cheaper options offer better overall value

=====================================================
FINAL RULES
=====================================================

- Return ONLY clean Markdown
- Do NOT return JSON
- Do NOT wrap markdown in code blocks
- Do NOT return explanations outside markdown
- Keep formatting professional and readable
";




}


}