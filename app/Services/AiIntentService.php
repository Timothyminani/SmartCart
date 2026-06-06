<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Support\Facades\Http;
use App\Models\ProductAttribute;

class AiIntentService
{
    public function extractIntent(string $query): array
    {
        $categories = Category::pluck('name')
            ->filter()
            ->values()
            ->toArray();

        $brands = Brand::pluck('name')
            ->filter()
            ->values()
            ->toArray();

        $attributesByCategory = Category::with([
            'products.attributes'
        ])->get();    


        $attributeText = '';

foreach ($attributesByCategory as $category) {

    $attributeNames = $category->products
        ->flatMap(function ($product) {
            return $product->attributes;
        })
        ->pluck('attribute_name')
        ->unique()
        ->sort()
        ->values()
        ->toArray();

    $attributeText .= "\n{$category->name}:\n";

    foreach ($attributeNames as $attribute) {
        $attributeText .= "- {$attribute}\n";
    }
}



        $systemPrompt = "You are an expert ecommerce AI intent extraction engine. Return ONLY valid JSON.";


$prompt = "
AVAILABLE CATEGORIES:
" . implode(', ', $categories) . "

AVAILABLE BRANDS:
" . implode(', ', $brands) . "

AVAILABLE ATTRIBUTES BY CATEGORY:
{$attributeText}

You are an AI shopping intent extraction engine for an electronics store.

Your task is to analyze the user's search query and extract their shopping intent in a structured format.

IMPORTANT:

Do NOT assume the user wants a computer.

Always choose the most appropriate category from AVAILABLE CATEGORIES based on the user's request.

Examples:

'Best phone for content creation'
=> Phones

'Tablet for school'
=> Tablets

'Speaker for parties'
=> Speakers

'Smartwatch for fitness'
=> Watches

'Fast charger for iPhone'
=> Chargers

'USB-C cable for Samsung'
=> Cables

'Laptop for programming'
=> Computers

Return ONLY valid JSON in this exact structure:

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

RULES

1. category

Return category as an object.

{
  \"primary\": null,
  \"alternatives\": []
}

RULES:

- primary must be the most likely category.
- alternatives may contain other relevant categories.
- Every category must come from AVAILABLE CATEGORIES.
- Never invent categories.
- If only one category is relevant, alternatives should be empty.

Examples:

User:
\"Laptop for programming\"

{
  \"primary\": \"Computers\",
  \"alternatives\": []
}

User:
\"Best device for content creation\"

{
  \"primary\": \"Phones\",
  \"alternatives\": [
    \"Tablets\",
    \"Computers\"
  ]
}

User:
\"Device for watching movies\"

{
  \"primary\": \"Tablets\",
  \"alternatives\": [
    \"Computers\",
    \"Phones\"
  ]
}

MULTI-CATEGORY RULE

Some user requests can be satisfied by more than one category.

Examples:

\"Content creation\"
=> Phones, Tablets, Computers

\"Watching movies\"
=> Tablets, Computers, Phones

\"School work\"
=> Tablets, Computers

\"Business use\"
=> Phones, Tablets, Computers

When multiple categories are relevant:

- Choose the most likely category as primary.
- Put the others in alternatives.
- Generate preferred_attributes_by_category for ALL categories.

2. brand
- Must be one of AVAILABLE BRANDS.
- Only return a brand if explicitly mentioned.

3. use_case
Possible values:

- Gaming
- Programming
- Business
- School
- Content Creation
- Video Editing
- Photography
- Music
- Fitness
- Entertainment
- Travel
- Home Use
- Office Use
- Everyday Use

4. budget_max
Extract numerical budgets when present.

Examples:

'under 50000' => 50000
'below 80000' => 80000
'around 100000' => 100000

5. budget_tier

Possible values:

- Budget
- Mid Range
- Premium

6. required_attributes

Extract attributes that the customer explicitly requires.

These attributes MUST be used as hard filters.

Examples:

\"I need a laptop with HDMI\"

{
  \"required_attributes\": {
    \"ports\": \"hdmi\"
  }
}

\"I need a phone with 256GB storage\"

{
  \"required_attributes\": {
    \"storage\": \"256gb\"
  }
}

\"I need a smartwatch with GPS\"

{
  \"required_attributes\": {
    \"gps\": true
  }
}



REQUIRED ATTRIBUTES RULE:

required_attributes MUST ONLY contain:
1. exact numeric values
2. boolean values (true/false)
3. standardized technical specs

DO NOT use descriptive words like:
- long_life
- strong
- good
- fast


If a required attribute is not measurable:
- convert it into a preferred_attributes_by_category signal instead


7. preferred_attributes_by_category

Infer preferred attributes for EACH relevant category.

These are NOT hard filters.

They are ranking signals used to score products.

When multiple categories are relevant, return preferences for every category.

Structure:

{
  \"Phones\": {},
  \"Tablets\": {},
  \"Computers\": {}
}

Only include categories that appear in:

- primary
- alternatives

STRICT RULE:

preferred_attributes_by_category MUST ONLY contain:
- normalized technical values
- no descriptive words
- no marketing language
- no subjective terms


8. priority_features

priority_features MUST describe user intent only.

For examples:
-programming
-gaming
-video_editing
-content_creation
-photography
-business
-school
-travel
-battery_focused
-heavy_compute
-entertainment
-office_use

DO NOT map these to attributes.

Do NOT invent new values.

Do NOT return:
- performance
- powerful
- strong
- fast
- good battery
- good camera

Mapping is handled by backend system only.

9. search_keywords

Generate useful search keywords that can help find matching products.

10. Examples of FULL INTENT EXTRACTION

Example 1:

USER:
I need a laptop with HDMI for programming

OUTPUT:

{
  \"category\": \"Computers\",
  \"brand\": null,
  \"use_case\": \"Programming\",
  \"budget_max\": null,
  \"budget_tier\": null,

  \"required_attributes\": {
    \"ports\": \"hdmi\"
  },

  \"preferred_attributes_by_category\": {
    \"Computers\": {
      \"ram\": \"16gb\",
      \"storage\": \"512gb ssd\"
    }
  },

  \"priority_features\": [
    \"performance\",
    \"battery life\",
    \"keyboard quality\"
  ],

  \"search_keywords\": [
    \"programming\",
    \"hdmi\",
    \"ssd\"
  ]
}

Example 2:

USER:
I'm a content creator. What devices do you have for me?

OUTPUT:

{
  \"category\": {
    \"primary\": \"Phones\",
    \"alternatives\": [
      \"Tablets\",
      \"Computers\"
    ]
  },

  \"brand\": null,
  \"use_case\": \"Content Creation\",
  \"budget_max\": null,
  \"budget_tier\": \"Mid Range\",

  \"required_attributes\": {},

  \"preferred_attributes_by_category\": {

    \"Phones\": {
      \"camera_rear\": \"48mp\",
      \"video_recording\": \"4k\",
      \"battery\": \"5000mah\"
    },

    \"Tablets\": {
      \"display\": \"fhd\",
      \"battery\": \"5000mah\"
    },

    \"Computers\": {
      \"ram\": \"16gb\",
      \"graphics\": \"rtx\",
      \"display\": \"color accurate\"
    }
  },

  \"priority_features\": [
    \"camera quality\",
    \"video recording\",
    \"battery life\"
  ],

  \"search_keywords\": [
    \"content creation\",
    \"camera\",
    \"video\"
  ]
}


ATTRIBUTE NORMALIZATION RULE:

You MUST normalize all attributes into consistent technical values.

DO NOT use vague phrases like:
- \"high resolution\"
- \"long battery\"
- \"good camera\"
- \"fast performance\"

Instead use structured values:

DISPLAY:
- \"fhd\"
- \"2k\"
- \"4k\"

BATTERY:
For required_attributes:
- ONLY extract battery if numeric value is explicitly mentioned (e.g. \"5000mah\")

If user does NOT specify a number:
- DO NOT output battery in required_attributes

Instead:
- put battery expectations into priority_features only

CAMERA:
- megapixels only (e.g. \"48mp\", \"108mp\")

RAM:
- \"4gb\", \"8gb\", \"16gb\"

STORAGE:
- \"128gb\", \"256gb\", \"512gb\"

GRAPHICS:
- \"integrated\", \"rtx\", \"radeon\"

WATER RESISTANCE:
- true / false only


ATTRIBUTE NAME RULE:

When generating:

- required_attributes
- preferred_attributes_by_category

You MUST use attribute names from the selected category's attribute list - AVAILABLE ATTRIBUTES BY CATEGORY section.

DO NOT invent attribute names.

BAD:

{
  \"performance\": \"high\"
}

{
  \"camera_quality\": \"high\"
}

{
  \"battery_strength\": \"high\"
}

GOOD:

{
  \"RAM\": \"16 GB\"
}

{
  \"Processor\": \"Intel Core i7\"
}

{
  \"Storage\": \"512 GB\"
}

If a user request refers to a concept that does not exist as an attribute name:

DO NOT create a new attribute.

Instead place it in:

priority_features or search_keywords.

Example:
Computers attributes:

- Processor
- RAM
- Storage
- Battery

User:
Laptop for programming

GOOD:

{
  \"preferred_attributes_by_category\": {
    \"Computers\": {
      \"Processor\": \"Intel Core i7\",
      \"RAM\": \"16 GB\"
    }
  }
}

BAD:

{
  \"preferred_attributes_by_category\": {
    \"Computers\": {
      \"performance\": \"high\"
    }
  }
}




ATTRIBUTE CLASSIFICATION RULE:

Hard filters (required_attributes):
- exact specs user explicitly demands
- numbers, ports, storage, RAM, CPU type
- boolean constraints

Soft signals (preferred_attributes_by_category):
- performance expectations
- battery strength
- camera quality
- display quality

If uncertain → ALWAYS treat as preferred attribute, NOT required
If a user says \"strong\", \"good\", \"best\", \"long\", \"fast\" → NEVER put in required_attributes. Always convert to preferred_attributes_by_category or priority_features.


USER QUERY:

{$query}
";

       $response = Http::withToken(config('services.replicate.token'))
    ->post(
        'https://api.replicate.com/v1/models/openai/gpt-4o-mini/predictions',
        [
            'input' => [
                'prompt' => $prompt,
                'system_prompt' => $systemPrompt,
                'max_tokens' => 400,
                'temperature' => 0.2,
            ]
        ]
    );
sleep(7); // initial wait before polling    
$prediction = $response->json();

$getUrl = $prediction['urls']['get'];

$status = $prediction['status'];

while (in_array($status, ['starting', 'processing'])) {

    sleep(1);

    $poll = Http::withToken(config('services.replicate.token'))
        ->get($getUrl);

    $prediction = $poll->json();

    $status = $prediction['status'];
}

$output = $prediction['output'] ?? null;


$text = is_array($output)
    ? implode('', $output)
    : $output;

$decoded = json_decode(trim($text), true);

return is_array($decoded)
    ? $decoded
    : [
        'category' => null,
        'brand' => null,
        'use_case' => null,
        'budget_max' => null,
        'budget_tier' => null,
        'min_specs' => [
            'ram' => null,
            'storage' => null,
        ],
        'recommended_specs' => [
            'ram' => null,
            'storage' => null,
        ],
        'priority_features' => [],
        'search_keywords' => [],
    ];


    if ($status === 'failed') {
    return [
        'category' => null,
        'brand' => null,
        'use_case' => null,
        'budget_max' => null,
        'budget_tier' => null,
        'min_specs' => [
            'ram' => null,
            'storage' => null,
        ],
        'recommended_specs' => [
            'ram' => null,
            'storage' => null,
        ],
        'priority_features' => [],
        'search_keywords' => [],
    ];
}



    }
}