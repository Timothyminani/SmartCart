<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderStatusUpdatedMail;

class AdminOrderController extends Controller
{
    /**
     * Display all orders.
     */
    public function index(Request $request)
{
    $query = Order::with('user');

    // Search filter
    if ($request->filled('search')) {

        $search = $request->search;

        $query->where(function ($q) use ($search) {

            $q->where('id', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('address', 'like', "%{$search}%")
              ->orWhereHas('user', function ($userQuery) use ($search) {

                  $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");

              });

        });
    }

    // Order status filter
    if ($request->filled('status')) {

        $query->where('status', $request->status);

    }

    // Payment status filter
    if ($request->filled('payment_status')) {

        $query->where('payment_status', $request->payment_status);

    }

    // Date filter
    if ($request->filled('date')) {

        switch ($request->date) {

            case 'today':
                $query->whereDate('created_at', today());
                break;

            case 'week':
                $query->where('created_at', '>=', now()->subDays(7));
                break;

            case 'month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;

            case 'year':
                $query->whereYear('created_at', now()->year);
                break;
        }
    }

    $orders = $query
        ->latest()
        ->paginate(10)
        ->withQueryString();

    return Inertia::render('Admin/Orders/Index', [
        'orders' => $orders,

        // preserve selected filters after refresh
        'filters' => [
            'search' => $request->search,
            'status' => $request->status,
            'payment_status' => $request->payment_status,
            'date' => $request->date,
        ]
    ]);
}

    /**
     * Display a single order.
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'items.product.images'
        ]);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
{
    $validated = $request->validate([
        'status' => [
            'required',
            'in:pending,processing,shipped,delivered,cancelled'
        ],

        'payment_status' => [
            'required',
            'in:pending,paid,failed,refunded'
        ]
    ]);

    $order->update([
        'status' => $validated['status'],
        'payment_status' => $validated['payment_status'],
    ]);

$order->load('user');

    Mail::to($order->user->email)
        ->queue(new OrderStatusUpdatedMail($order));


    return back()->with('success', 'Order updated successfully.');
}
}