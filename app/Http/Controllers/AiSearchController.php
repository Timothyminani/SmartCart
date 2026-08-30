<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AiIntentService;
use App\Services\ProductSearchService;
use App\Services\AiExplanationService;

class AiSearchController extends Controller
{
    public function __construct(
        protected AiIntentService $aiIntentService,
        protected ProductSearchService $productSearchService,
        protected AiExplanationService $aiExplanationService
    ) {
    }

    public function search(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. VALIDATE QUERY
        |--------------------------------------------------------------------------
        */

        $query = trim((string) $request->input('query'));

        if ($query === '') {
            return response()->json([
                'message' => 'Please enter a search query.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 2. EXTRACT + VALIDATE INTENT
        |--------------------------------------------------------------------------
        |
        | AiIntentService now handles:
        | - intent extraction
        | - catalog validation
        | - one AI correction attempt when necessary
        | - normalization
        |
        */

        $intent = $this->aiIntentService->extractIntent($query);

        /*
        |--------------------------------------------------------------------------
        | 3. SEARCH + RANK PRODUCTS
        |--------------------------------------------------------------------------
        */

        $products = $this->productSearchService->search($intent);

        /*
        |--------------------------------------------------------------------------
        | 4. GENERATE BUYING EXPLANATION
        |--------------------------------------------------------------------------
        |
        | Only the top 3 ranked products are sent to the explanation AI.
        | AiExplanationService handles empty results itself.
        |
        */

        $aiExplanation = $this->aiExplanationService->generate(
            $query,
            $intent,
            $products->take(3)
        );

        /*
        |--------------------------------------------------------------------------
        | 5. RETURN RESULTS
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'query' => $query,
            'intent' => $intent,
            'products' => $products,
            'ai_explanation' => $aiExplanation['ai_explanation'],
            'refinement_suggestions' => $aiExplanation['refinement_suggestions'],
        ]);
    }
}