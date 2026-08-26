<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\Models\Review;
use Illuminate\Support\Str;

class ProductControllerPublic extends Controller
{

public function index(Request $request)
{
    $query = $request->query('query');
    $sort = $request->query('sort');

    $categoryIds = $request->query('categories', []);
    $brandIds = $request->query('brands', []);

    $productsQuery = Product::with([
        'category',
        'brand',
        'images',
        'reviews',
    ]);


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

if (!empty($query)) {

    // Remove unnecessary spaces
    $search = trim($query);

    // Split search into individual words
    $terms = preg_split('/\s+/', $search);

    foreach ($terms as $term) {

        // Handle basic plural/singular cases:
        // laptops -> laptop
        // phones -> phone
        $singularTerm = Str::singular($term);

        $productsQuery->where(function ($q) use ($term, $singularTerm) {

            /*
            |--------------------------------------------------------------------------
            | PRODUCT NAME
            |--------------------------------------------------------------------------
            */

            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('name', 'like', '%' . $singularTerm . '%')


            /*
            |--------------------------------------------------------------------------
            | DESCRIPTION
            |--------------------------------------------------------------------------
            */

              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $singularTerm . '%')


            /*
            |--------------------------------------------------------------------------
            | BRAND
            |--------------------------------------------------------------------------
            */

              ->orWhereHas('brand', function ($brandQuery) use ($term, $singularTerm) {

                  $brandQuery
                      ->where('name', 'like', '%' . $term . '%')
                      ->orWhere('name', 'like', '%' . $singularTerm . '%');

              })


            /*
            |--------------------------------------------------------------------------
            | CATEGORY
            |--------------------------------------------------------------------------
            */

              ->orWhereHas('category', function ($categoryQuery) use ($term, $singularTerm) {

                  $categoryQuery
                      ->where('name', 'like', '%' . $term . '%')
                      ->orWhere('name', 'like', '%' . $singularTerm . '%');

              })


            ->orWhereHas('attributes', function ($attributeQuery) use ($term, $singularTerm) {

                $attributeQuery
                    ->where('attribute_name', 'like', '%' . $term . '%')
                    ->orWhere('attribute_value', 'like', '%' . $term . '%')
                    ->orWhere('attribute_name', 'like', '%' . $singularTerm . '%')
                    ->orWhere('attribute_value', 'like', '%' . $singularTerm . '%');

            });



              
        });


        

    }
}

    /*
    |--------------------------------------------------------------------------
    | FILTER BY CATEGORY
    |--------------------------------------------------------------------------
    */

    if (!empty($categoryIds)) {

        $productsQuery->whereIn(
            'category_id',
            $categoryIds
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FILTER BY BRAND
    |--------------------------------------------------------------------------
    */

    if (!empty($brandIds)) {

        $productsQuery->whereIn(
            'brand_id',
            $brandIds
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SORTING
    |--------------------------------------------------------------------------
    */

    if ($sort === 'price_asc') {

        $productsQuery->orderBy(
            'price',
            'asc'
        );

    } elseif ($sort === 'price_desc') {

        $productsQuery->orderBy(
            'price',
            'desc'
        );

    } else {

        $productsQuery->latest();

    }


    $products = $productsQuery
        ->paginate(12)
        ->withQueryString();


    $categories = Category::all();

    $brands = Brand::all();


    return Inertia::render(
        'Products/Index',
        [
            'query' => $query,

            'products' => $products,

            'categories' => $categories,

            'brands' => $brands,

            'filters' => [
                'categories' => $categoryIds,
                'brands' => $brandIds,
            ],
        ]
    );
}










public function show(Product $product)
{
    $product->load([
        'category',
        'brand',
        'images',
        'attributes',
        'reviews.user',
    ]);

    // Rating information
    $averageRating = $product->reviews->avg('rating');
    $reviewCount = $product->reviews->count();

    /*
    |--------------------------------------------------------------------------
    | SMART RECOMMENDATION LOGIC
    |--------------------------------------------------------------------------
    */

    // PRODUCT PRICE
    $price = $product->sale_price ?? $product->price;

    // PRICE RANGE
    $minPrice = $price - 10000;
    $maxPrice = $price + 10000;

    $relatedProducts = Product::with([
            'brand',
            'images'
        ])
        ->where('id', '!=', $product->id)

        // SAME CATEGORY
        ->where('category_id', $product->category_id)

        // SIMILAR PRICE RANGE
        ->whereBetween('sale_price', [
            max(0, $minPrice),
            $maxPrice
        ])

        // PRIORITIZE SAME BRAND
        ->orderByRaw("
            CASE
                WHEN brand_id = ? THEN 0
                ELSE 1
            END
        ", [$product->brand_id])

        ->latest()
        ->take(10)
        ->get();

    return Inertia::render('Products/Show', [
        'product' => $product,
        'relatedProducts' => $relatedProducts,

        // Rating data
        'averageRating' => $averageRating
            ? round($averageRating, 1)
            : 0,

        'reviewCount' => $reviewCount,
    ]);
}




}
