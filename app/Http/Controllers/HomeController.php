<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | Popular Categories
        |--------------------------------------------------------------------------
        */

        $popularCategories = Category::query()
            ->where('is_featured', true)
            ->whereNotNull('image')
            ->latest()
            ->take(6)
            ->get([
                'id',
                'name',
                'slug',
                'image',
                'is_featured',
            ]);


        /*
        |--------------------------------------------------------------------------
        | Featured Products
        |--------------------------------------------------------------------------
        */

        $featuredProducts = Product::with([
                'category',
                'brand',
                'images',
            ])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->take(8)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Home Page
        |--------------------------------------------------------------------------
        */

        return Inertia::render('Home', [
            'popularCategories' => $popularCategories,
            'featuredProducts' => $featuredProducts,
            'selectedCategory' => null,
        ]);
    }
}