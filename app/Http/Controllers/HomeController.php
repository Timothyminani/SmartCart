<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
{
    $popularCategories = Category::whereHas('products', function ($query) {
            $query->where('is_featured', true)
                  ->where('is_active', true)
                  ->where('stock_quantity', '>', 0);
        })
        ->withCount([
            'products as featured_products_count' => function ($query) {
                $query->where('is_featured', true)
                      ->where('is_active', true)
                      ->where('stock_quantity', '>', 0);
            }
        ])
        ->orderByDesc('featured_products_count')
        ->take(8)
        ->get();

    $featuredProducts = Product::with(['category', 'brand', 'images'])
        ->where('is_featured', true)
        ->where('is_active', true)
        ->where('stock_quantity', '>', 0)
        ->latest()
        ->take(8)
        ->get();

    return Inertia::render('Home', [
        'popularCategories' => $popularCategories,
        'featuredProducts' => $featuredProducts,
        'selectedCategory' => null,
    ]);
}
}