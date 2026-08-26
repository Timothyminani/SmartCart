<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'phone' => [
                'required',
                'string',
                'max:20',
            ],

            'address' => [
                'required',
                'string',
                'max:255',
            ],

            'delivery' => [
                'required',
                Rule::in([
                    'standard',
                    'express',
                    'pickup',
                ]),
            ],

            'payment_method' => [
                'required',
                Rule::in([
                    'cod',
                    'mpesa',
                ]),
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | CART
        |--------------------------------------------------------------------------
        */

        $cart = Cart::with([
            'items.product',
        ])
            ->where(
                'user_id',
                auth()->id()
            )
            ->first();


        if (
            !$cart ||
            $cart->items->isEmpty()
        ) {
            return response()->json([
                'error' => 'Cart is empty.',
            ], 400);
        }


        /*
        |--------------------------------------------------------------------------
        | SUBTOTAL
        |--------------------------------------------------------------------------
        */

        $subtotal = $cart->items->sum(
            function ($item) {
                return
                    $item->price *
                    $item->quantity;
            }
        );


        /*
        |--------------------------------------------------------------------------
        | DELIVERY FEE
        |--------------------------------------------------------------------------
        */

        $deliveryFee = match (
            $validated['delivery']
        ) {
            'standard' => 250,
            'express' => 500,
            'pickup' => 0,
        };


        $total =
            $subtotal +
            $deliveryFee;


        /*
        |--------------------------------------------------------------------------
        | CASH ON DELIVERY
        |--------------------------------------------------------------------------
        */

        if (
            $validated['payment_method'] === 'cod'
        ) {
            $order = DB::transaction(
                function () use (
                    $cart,
                    $validated,
                    $deliveryFee,
                    $total
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ORDER
                    |--------------------------------------------------------------------------
                    */

                    $order = Order::create([
                        'user_id' =>
                            auth()->id(),

                        'total_amount' =>
                            $total,

                        'status' =>
                            'pending',

                        'payment_status' =>
                            'unpaid',

                        'phone' =>
                            $validated['phone'],

                        'address' =>
                            $validated['address'],

                        'delivery_method' =>
                            $validated['delivery'],

                        'delivery_fee' =>
                            $deliveryFee,

                        'payment_method' =>
                            'cod',
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | CREATE ITEMS + LOCK STOCK
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $cart->items as $item
                    ) {
                        /*
                         * Lock the product row while this
                         * transaction is processing it.
                         *
                         * This prevents two customers from
                         * successfully buying the last unit
                         * at exactly the same time.
                         */

                        $product = Product::query()
                            ->whereKey(
                                $item->product_id
                            )
                            ->lockForUpdate()
                            ->firstOrFail();


                        /*
                        |--------------------------------------------------------------------------
                        | STOCK VALIDATION
                        |--------------------------------------------------------------------------
                        */

                        if (
                            $item->quantity >
                            $product->stock_quantity
                        ) {
                            abort(
                                response()->json([
                                    'error' =>
                                        "{$product->name} only has {$product->stock_quantity} item(s) left in stock.",
                                ], 422)
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ORDER ITEM
                        |--------------------------------------------------------------------------
                        */

                        OrderItem::create([
                            'order_id' =>
                                $order->id,

                            'product_id' =>
                                $product->id,

                            'quantity' =>
                                $item->quantity,

                            'price' =>
                                $item->price,
                        ]);


                        /*
                        |--------------------------------------------------------------------------
                        | REDUCE STOCK
                        |--------------------------------------------------------------------------
                        */

                        $product->decrement(
                            'stock_quantity',
                            $item->quantity
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ADMIN NOTIFICATION
                    |--------------------------------------------------------------------------
                    */

                    AdminNotification::create([
                        'title' =>
                            'New Order Received',

                        'message' =>
                            auth()->user()->name .
                            ' placed order #' .
                            $order->id .
                            ' worth KES ' .
                            number_format(
                                $order->total_amount
                            ),

                        'type' =>
                            'order',

                        'url' =>
                            '/admin/orders/' .
                            $order->id,
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | CLEAR CART
                    |--------------------------------------------------------------------------
                    */

                    $cart
                        ->items()
                        ->delete();


                    return $order;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | LOAD RELATIONSHIPS
            |--------------------------------------------------------------------------
            */

            $order->load([
                'user',
                'items.product',
            ]);


            /*
            |--------------------------------------------------------------------------
            | EMAILS - ENABLE LATER
            |--------------------------------------------------------------------------
            */

            // Mail::to($order->user->email)
            //     ->queue(
            //         new OrderPlacedMail($order)
            //     );

            // Mail::to('admin@smartcart.test')
            //     ->queue(
            //         new NewOrderAdminMail($order)
            //     );


            return response()->json([
                'type' =>
                    'cod',

                'order_id' =>
                    $order->id,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | M-PESA
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | At this point the customer has NOT paid.
        |
        | Therefore:
        | - do not reduce stock here
        | - do not clear the cart here
        | - do not mark the order paid
        |
        | Those actions should happen only after
        | M-Pesa confirms successful payment.
        |
        */

        $result = DB::transaction(
            function () use (
                $cart,
                $validated,
                $deliveryFee,
                $total
            ) {

                /*
                |--------------------------------------------------------------------------
                | VALIDATE CURRENT STOCK
                |--------------------------------------------------------------------------
                |
                | This confirms that checkout can begin.
                |
                | We still MUST check stock again after
                | successful M-Pesa payment because stock
                | is not being reserved here.
                |
                */

                foreach (
                    $cart->items as $item
                ) {
                    $product = Product::query()
                        ->findOrFail(
                            $item->product_id
                        );


                    if (
                        $item->quantity >
                        $product->stock_quantity
                    ) {
                        abort(
                            response()->json([
                                'error' =>
                                    "{$product->name} only has {$product->stock_quantity} item(s) left in stock.",
                            ], 422)
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CREATE PENDING ORDER
                |--------------------------------------------------------------------------
                */

                $order = Order::create([
                    'user_id' =>
                        auth()->id(),

                    'total_amount' =>
                        $total,

                    'status' =>
                        'pending',

                    'payment_status' =>
                        'pending',

                    'phone' =>
                        $validated['phone'],

                    'address' =>
                        $validated['address'],

                    'delivery_method' =>
                        $validated['delivery'],

                    'delivery_fee' =>
                        $deliveryFee,

                    'payment_method' =>
                        'mpesa',
                ]);


                /*
                |--------------------------------------------------------------------------
                | CREATE ORDER ITEMS
                |--------------------------------------------------------------------------
                */

                foreach (
                    $cart->items as $item
                ) {
                    OrderItem::create([
                        'order_id' =>
                            $order->id,

                        'product_id' =>
                            $item->product_id,

                        'quantity' =>
                            $item->quantity,

                        'price' =>
                            $item->price,
                    ]);
                }


                /*
                |--------------------------------------------------------------------------
                | CREATE PENDING PAYMENT
                |--------------------------------------------------------------------------
                */

                $payment = Payment::create([
                    'order_id' =>
                        $order->id,

                    'user_id' =>
                        auth()->id(),

                    'amount' =>
                        $total,

                    'phone' =>
                        $validated['phone'],

                    'status' =>
                        'pending',

                    'address' =>
                        $validated['address'],

                    'delivery_method' =>
                        $validated['delivery'],

                    'payment_method' =>
                        'mpesa',
                ]);


                return [
                    'order' =>
                        $order,

                    'payment' =>
                        $payment,
                ];
            }
        );


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'type' =>
                'mpesa',

            'payment_id' =>
                $result['payment']->id,

            'order_id' =>
                $result['order']->id,
        ]);
    }
}