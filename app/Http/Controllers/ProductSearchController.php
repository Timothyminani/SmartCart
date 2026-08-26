<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductSearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $query = trim($request->query('q', ''));

        if (strlen($query) < 2) {
            return response()->json([
                'products' => [],
            ]);
        }

        $terms = preg_split('/\s+/', $query);

        $products = Product::with(['brand', 'category', 'images'])
            ->where('is_active', true)
            ->where(function ($mainQuery) use ($terms) {

                foreach ($terms as $term) {

                    $singularTerm = Str::singular($term);

                    $mainQuery->where(function ($q) use ($term, $singularTerm) {

                        $q->where('name', 'like', '%' . $term . '%')
                            ->orWhere('name', 'like', '%' . $singularTerm . '%')

                            ->orWhere('description', 'like', '%' . $term . '%')
                            ->orWhere('description', 'like', '%' . $singularTerm . '%')

                            ->orWhereHas('brand', function ($brandQuery) use ($term, $singularTerm) {

                                $brandQuery
                                    ->where('name', 'like', '%' . $term . '%')
                                    ->orWhere('name', 'like', '%' . $singularTerm . '%');

                            })

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

            })
            ->take(5)
            ->get()
            ->map(function ($product) {

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'sale_price' => $product->sale_price,
                    'price' => $product->price,
                    'brand' => $product->brand?->name,
                    'category' => $product->category?->name,
                    'image' => $product->images->first()?->image_path,
                ];

            });

        return response()->json([
            'products' => $products,
        ]);
    }
}