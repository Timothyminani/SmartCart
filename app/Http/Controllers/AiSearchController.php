<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AiIntentService;
use App\Services\ProductSearchService;
use App\Services\IntentValidationService;
use App\Services\AiExplanationService;

class AiSearchController extends Controller
{
    protected $aiIntentService;
    protected $productSearchService;
    protected $intentValidationService;
    protected $aiExplanationService;

    public function __construct(
        AiIntentService $aiIntentService,
        ProductSearchService $productSearchService,
        IntentValidationService $intentValidationService,
        AiExplanationService $aiExplanationService
    ) {
        $this->aiIntentService = $aiIntentService;
        $this->productSearchService = $productSearchService;
        $this->intentValidationService = $intentValidationService;
        $this->aiExplanationService = $aiExplanationService;
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return back()->with('error', 'Please enter a search query.');
        }

        // =========================
        // 1. EXTRACT INTENT (AI)
        // =========================
        $intent = $this->aiIntentService->extractIntent($query);

        // =========================
        // 2. VALIDATE INTENT
        // =========================
       // $validatedIntent = $this->intentValidationService->validate($intent);

        // =========================
        // 3. SEARCH PRODUCTS
        // =========================
        $products = $this->productSearchService->search($intent);

        // =========================
        // 4. AI EXPLANATION
        // =========================
        $aiExplanation = $this->aiExplanationService->generate(
            $query,
            $intent,
            $products->take(3) 
        );

        // =========================
        // 5. RETURN RESULTS
        // =========================
       
 return response()->json([
    'query' => $query,
    'intent' => $intent,
    'products' => $products,
    'ai_explanation' => $aiExplanation,
]);

    }
}