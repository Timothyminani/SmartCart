<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Mail\OrderPlacedMail;
use App\Mail\NewOrderAdminMail;
use Illuminate\Support\Facades\Mail;

class CheckoutController extends Controller
{
   

public function store(Request $request)
{
    $request->validate([
        'phone' => 'required|string|max:20',
        'address' => 'required|string|max:255',
        'delivery' => 'required|string',
        'payment_method' => 'required|string'
    ]);

    $cart = Cart::with('items.product')
        ->where('user_id', auth()->id())
        ->first();

    if (!$cart || $cart->items->isEmpty()) {
        return response()->json([
            'error' => 'Cart is empty'
        ], 400);
    }

    // subtotal
    $subtotal = $cart->items->sum(function ($item) {
        return $item->price * $item->quantity;
    });

    // delivery fee
    $deliveryFee = match ($request->delivery) {
        'standard' => 250,
        'express' => 500,
        'pickup' => 0,
        default => 0
    };

    $total = $subtotal + $deliveryFee;

    /*
    |--------------------------------------------------------------------------
    | CASH ON DELIVERY
    |--------------------------------------------------------------------------
    */

    if ($request->payment_method === 'cod') {

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_amount' => $total,
            'status' => 'pending',
            'payment_status' => 'unpaid',
            'phone' => $request->phone,
            'address' => $request->address,
            'delivery_method' => $request->delivery,
            'delivery_fee' => $deliveryFee,
            'payment_method' => 'cod'
        ]);

        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ]);
        }

                // Load relationships for email
        $order->load('user', 'items.product');

        // Customer email
        Mail::to($order->user->email)
            ->queue(new OrderPlacedMail($order));

            // Admin email
        Mail::to('admin@smartcart.test')
            ->queue(new NewOrderAdminMail($order));

       // 4. Clear Cart
        $cart->items()->delete();

        return response()->json([
            'type' => 'cod',
            'order_id' => $order->id
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | ONLINE PAYMENT (MPESA)
    |--------------------------------------------------------------------------
    */



// 1. Create Order
$order = Order::create([
    'user_id' => auth()->id(),
    'total_amount' => $total,
    'status' => 'pending',
    'payment_status' => 'pending',
    'phone' => $request->phone,
    'address' => $request->address,
    'delivery_method' => $request->delivery,
    'delivery_fee' => $deliveryFee,
    'payment_method' => 'mpesa'
]);

// 2. Create Order Items
foreach ($cart->items as $item) {

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $item->product_id,
        'quantity' => $item->quantity,
        'price' => $item->price,
    ]);

}



// 3. Create Payment
$payment = Payment::create([
    'order_id' => $order->id,
    'user_id' => auth()->id(),
    'amount' => $total,
    'phone' => $request->phone,
    'status' => 'pending',
    'address' => $request->address,
    'delivery_method' => $request->delivery,
    'payment_method' => 'mpesa'   
]);




// Load relationships
$order->load('user', 'items.product');

// Customer email
  Mail::to($order->user->email)
    ->queue(new OrderPlacedMail($order));

 // Admin email
 Mail::to('admin@smartcart.test')
      ->queue(new NewOrderAdminMail($order));

// 4. Clear Cart
$cart->items()->delete();

// 5. Redirect
return response()->json([
    'type' => 'mpesa',
    'payment_id' => $payment->id
]);
}







}