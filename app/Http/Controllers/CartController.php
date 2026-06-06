<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;


class CartController extends Controller
{
    // ---------------- ADD ----------------
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => auth()->id()
        ]);

        $item = $cart->items()
            ->where('product_id', $request->product_id)
            ->first();

        if ($item) {
            $item->increment('quantity');
        } else {
            $product = Product::findOrFail($request->product_id);

            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ---------------- UPDATE ----------------
    public function update(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $item = CartItem::where('id', $request->item_id)
            ->whereHas('cart', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->firstOrFail();

        $item->update([
            'quantity' => $request->quantity
        ]);

        return response()->json(['success' => true]);
    }

    // ---------------- GET CART ----------------
    public function getCart()
    {
        $cart = Cart::with('items.product.images')
            ->where('user_id', auth()->id())
            ->first();

        return response()->json([
            'cart' => $cart ? $cart->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'image' => optional($item->product->images->first())->image_path,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                ];
            }) : []
        ]);
    }

    // ---------------- REMOVE ----------------
    public function remove(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:cart_items,id'
        ]);

        $item = CartItem::where('id', $request->item_id)
            ->whereHas('cart', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->firstOrFail();

        $item->delete();

        return response()->json(['success' => true]);
    }

    // ---------------- CLEAR ----------------
    public function clear()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        if ($cart) {
            $cart->items()->delete();
        }

        return response()->json(['success' => true]);
    }






public function recommendations()
{
    $cart = Cart::with('items.product')
        ->where('user_id', auth()->id())
        ->first();

    if (!$cart || $cart->items->isEmpty()) {
        return response()->json([
            'products' => []
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CART PRODUCT IDS
    |--------------------------------------------------------------------------
    */

    $cartProductIds = $cart->items
        ->pluck('product_id');

    /*
    |--------------------------------------------------------------------------
    | CATEGORY IDS
    |--------------------------------------------------------------------------
    */

    $categoryIds = $cart->items
        ->pluck('product.category_id')
        ->unique();

    /*
    |--------------------------------------------------------------------------
    | BRAND IDS
    |--------------------------------------------------------------------------
    */

    $brandIds = $cart->items
        ->pluck('product.brand_id')
        ->unique();

    /*
    |--------------------------------------------------------------------------
    | FETCH RECOMMENDATIONS
    |--------------------------------------------------------------------------
    */

    $products = Product::with([
            'brand',
            'images'
        ])

        // EXCLUDE PRODUCTS ALREADY IN CART
        ->whereNotIn('id', $cartProductIds)

        // SAME CATEGORY
        ->whereIn('category_id', $categoryIds)

        // PRIORITIZE SAME BRANDS
        ->orderByRaw("
            CASE
                WHEN brand_id IN (" . $brandIds->implode(',') . ")
                THEN 0
                ELSE 1
            END
        ")

        ->latest()
        ->take(10)
        ->get();

    return response()->json([
        'products' => $products
    ]);
}












}