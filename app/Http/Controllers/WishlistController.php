<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function products(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return response()->json([]);
        }

        $products = Product::with([
                'category',
                'brand',
                'images'
            ])
            ->whereIn('id', $ids)
            ->where('is_active', true)
            ->get();

        return response()->json($products);
    }
}