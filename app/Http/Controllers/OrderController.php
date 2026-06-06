<?php

namespace App\Http\Controllers;
use App\Models\Order;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\OrderItem;

class OrderController extends Controller
{
    
public function success($id)
{
    $order = Order::with('items.product.images')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    return Inertia::render('OrderSuccess', [
        'order' => $order
    ]);
}




public function index()
{
    $orders = Order::with('items.product.images')
        ->where('user_id', auth()->id())
        ->latest()
        ->get();

    return Inertia::render('Orders/Index', [
        'orders' => $orders
    ]);
}


public function show($id)
{
    $order = Order::with('items.product.images')
        ->where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    return Inertia::render('Orders/Show', [
        'order' => $order
    ]);
}




}
