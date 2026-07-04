<?php

namespace App\Services\Dashboard;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class DashboardAnalyticsService
{

public function stats()
{
    return [

        // Products
        'products' => Product::count(),

        // Orders
        'orders' => Order::count(),

        // Users
        'users' => User::count(),

        // Revenue
        'revenue' => Order::where('payment_status', 'paid')
            ->sum('total_amount'),

        // Out of stock
        'outOfStock' => Product::where('stock_quantity', 0)
            ->count(),

        // Low stock
        'lowStock' => Product::where('stock_quantity', '<=', 5)
            ->where('stock_quantity', '>', 0)
            ->count(),

        // Pending orders
        'pendingOrders' => Order::where('status', 'pending')
            ->count(),

        // Completed orders
        'completedOrders' => Order::where('status', 'delivered')
            ->count(),
    ];
}



public function revenueChart()
{
    $monthlyRevenue = Order::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as revenue')
        )
        ->where('payment_status', 'paid')
        ->whereYear('created_at', now()->year)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy(DB::raw('MONTH(created_at)'))
        ->get();

    $months = [
        'Jan','Feb','Mar','Apr','May','Jun',
        'Jul','Aug','Sep','Oct','Nov','Dec'
    ];

    $revenueData = array_fill(0, 12, 0);

    foreach ($monthlyRevenue as $row) {
        $revenueData[$row->month - 1] = (float) $row->revenue;
    }

    return [
        'labels' => $months,
        'data'   => $revenueData,
    ];
}


public function orderStatusChart()
{
    return [
        'labels' => [
            'Pending',
            'Completed',
            'Cancelled'
        ],

        'data' => [
            Order::where('status', 'pending')->count(),
            Order::where('status', 'delivered')->count(),
            Order::where('status', 'cancelled')->count(),
        ]
    ];
}


public function categoryChart()
{
    $categories = Category::withCount('products')
        ->orderByDesc('products_count')
        ->take(5)
        ->get();

    return [
        'labels' => $categories->pluck('name'),
        'data'   => $categories->pluck('products_count'),
    ];
}


public function recentOrders()
{
    return Order::with('user')
        ->latest()
        ->take(5)
        ->get()
        ->map(function ($order) {
            return [
                'id' => $order->id,
                'customer' => $order->user?->name ?? 'Guest',
                'total' => $order->total_amount,
                'status' => $order->status,
                'date' => $order->created_at->format('d M Y'),
            ];
        });
}


}