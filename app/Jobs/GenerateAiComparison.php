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

Attributes:
";

            foreach ($product->attributes as $attribute) {

                $productText .= "- {$attribute->attribute_name}: {$attribute->attribute_value}\n";
            }

            $productText .= "\n";
        }

        return "
You are an advanced ecommerce AI product comparison engine.

Your job is to act like a professional product analyst (like Amazon + Tech reviewer combined).

Compare the products below using structured reasoning.

$productText

=====================================================
CRITICAL INSTRUCTIONS
=====================================================

- Return ONLY valid JSON
- No markdown, no explanation, no extra text
- DO NOT include objects inside arrays
- ALL arrays must contain ONLY STRINGS
- Be analytical, not descriptive
- Do NOT copy product names into sentences unless necessary

=====================================================
EVALUATION FRAMEWORK
=====================================================

You MUST evaluate products using these dimensions:

1. Performance (CPU, GPU, RAM)
2. Display quality
3. Battery life
4. Storage
5. Build quality
6. Price vs value
7. Target use case fit

Each comparison MUST be meaningful and not generic.

=====================================================
SCORING RULES
=====================================================

- Scores range from 0 to 10
- Higher score = better in that category
- Be realistic and comparative between products

=====================================================
OUTPUT STRUCTURE (STRICT)
=====================================================

{
  \"summary\": \"Executive summary\",

  \"winner\": {
    \"product_name\": \"Short product name\",
    \"overall_score\": 91,
    \"reason\": \"Why this product wins overall\"
  },

  \"best_for\": {
    \"gaming\": \"Short product name\",
    \"business\": \"Short product name\",
    \"students\": \"Short product name\",
    \"content_creation\": \"Short product name\",
    \"travel\": \"Short product name\"
  },

  \"comparison\": {
    \"performance\": {
      \"winner\": \"Short product name\",
      \"reason\": \"Why it wins\"
    },
    \"display\": {
      \"winner\": \"Short product name\",
      \"reason\": \"Why it wins\"
    },
    \"battery\": {
      \"winner\": \"Short product name\",
      \"reason\": \"Why it wins\"
    },
    \"storage\": {
      \"winner\": \"Short product name\",
      \"reason\": \"Why it wins\"
    },
    \"value_for_money\": {
      \"winner\": \"Short product name\",
      \"reason\": \"Why it wins\"
    }
  },

  \"scores\": {
    \"Short product name\": {
      \"overall\": 82
    },
    \"Short product name\": {
      \"overall\": 91
    }
  }
}


IMPORTANT:

A more expensive product should NOT automatically win.

The overall winner must be determined by balancing:

- Performance
- Features
- User experience
- Value for money
- Intended audience

If one product is significantly more expensive, justify why the additional cost is or is not worthwhile.

DECISION RULES

When determining an overall winner:

- Performance has the highest importance for gaming laptops
- Battery and portability have the highest importance for business laptops
- Display quality has high importance for creator laptops
- Value for money must always be considered

Do not treat all categories equally.

The winner should reflect the intended use case of the product category.


SCORING RULES

Overall score must be calculated from:

- Performance
- Display
- Battery
- Storage
- Value for money

The score must reflect actual differences between products.

Avoid giving products nearly identical scores unless they are genuinely very similar.


VALUE FOR MONEY RULE

A product is not automatically better because it is more expensive.

If a product costs significantly more, determine whether the additional features justify the additional cost.

Price should influence the final recommendation.


The summary must be 2-3 sentences.

Explain:

- Main difference
- Who should buy Product A
- Who should buy Product B


IMPORTANT NAMING RULE:

- NEVER use product_id in output
- ALWAYS use short product name (without brand) in output
- product_1 and product_2 labels are NOT allowed


IMPORTANT WINNER RULE (IMPORTANT):

The overall winner MUST be the product that wins most weighted categories:

- Performance = 30%
- Display = 15%
- Battery = 15%
- Storage = 10%
- Value = 30%

If a product wins value but loses all performance categories, it cannot be overall winner.



BEST_FOR RULE (VERY IMPORTANT):

Each category MUST include:

- product_name
- reason (why it fits that use case)

Format:

\"gaming\": {
  \"product_name\": \"HP Spectre x360\",
  \"reason\": \"Better GPU performance makes it more suitable for light gaming and creative workloads\"
}

\"reason\": \"...why this fits user type...\",
  \"tradeoff\": \"...what you sacrifice by choosing it...\"


BATTERY COMPARISON RULES

When evaluating battery:

1. Compare battery capacity (Wh) first.
2. Compare rated battery life (hours) second.
3. Higher Wh generally indicates larger battery capacity.
4. Longer rated battery life indicates better endurance.
5. If one product has both higher Wh and longer battery life, it MUST win the battery category.
6. Do NOT choose a winner because a battery is \"sufficient\", \"adequate\", or \"good enough\".
7. Battery winner must be determined from specifications, not user type.

Examples:

70Wh + 18 hours > 50Wh + 15 hours

83Wh + 15 hours > 57Wh + 15 hours

If battery specifications clearly favor one product, do NOT declare the weaker battery the winner.



PERFORMANCE COMPARISON RULES

Compare:

- CPU generation
- CPU core count
- CPU architecture
- GPU capability
- RAM capacity

A product with a significantly stronger CPU and GPU should win performance.

Do NOT select a weaker processor because it is cheaper.


STORAGE COMPARISON RULES

Compare:

- Capacity
- SSD vs eMMC
- SSD generation if available

Larger and faster storage wins.

Value-for-money considerations belong in the value category, not storage.


DISPLAY COMPARISON RULES

Compare:

- Resolution
- Panel technology (OLED, IPS, Mini LED)
- Refresh rate
- Brightness
- Color accuracy

A display with superior specifications should win display.

Do NOT choose a weaker display because it is sufficient for basic users.





=====================================================
FINAL RULES
=====================================================

- Be objective and analytical
- Do NOT be generic
- DO NOT repeat same point in multiple sections
- Every section must add new insight
- DO NOT output invalid JSON under any condition
";
    }
}