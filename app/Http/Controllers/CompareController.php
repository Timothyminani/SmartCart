<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\AiComparison;

use App\Jobs\GenerateAiComparison;

use Inertia\Inertia;

class CompareController extends Controller
{
    /**
     * LOAD COMPARE PAGE
     */
    public function index(Request $request)
    {
        // GET IDS
        $productIds = $request->products ?? [];

        // FETCH PRODUCTS
        $products = Product::with([
            'images',
            'brand',
            'category',
            'attributes'
        ])
        ->whereIn('id', $productIds)
        ->get();

        // RETURN PAGE
        return Inertia::render('Compare/Index', [
            'products' => $products
        ]);
    }

    /**
     * GENERATE AI COMPARISON
     */
    public function generateAi(Request $request)
    {
        // VALIDATE
        $request->validate([
            'products' => ['required', 'array', 'size:2']
        ]);

        // FETCH PRODUCTS
        $products = Product::with([
            'attributes',
            'brand',
            'category'
        ])
        ->whereIn('id', $request->products)
        ->get();

        // MUST BE EXACTLY 2
        if ($products->count() !== 2) {

            return response()->json([
                'message' => 'Two products are required'
            ], 422);
        }

        // CREATE COMPARISON RECORD
        $comparison = AiComparison::create([
            'product_ids' => $request->products,
            'status' => 'pending'
        ]);

        // DISPATCH JOB
        GenerateAiComparison::dispatch(
            $comparison->id
        );

        // RETURN RESPONSE
        return response()->json([
            'comparison_id' => $comparison->id,
            'status' => 'processing'
        ]);
    }

    /**
     * GET AI RESULT
     */
    public function aiResult(AiComparison $comparison)
    {
        return response()->json([
            'status' => $comparison->status,
            'result' => $comparison->result,
            'error' => $comparison->error
        ]);
    }
}