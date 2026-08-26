<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Order;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $customers = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
            'filters' => $request->only('search'),
            'stats' => [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
                'newThisMonth' => User::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ]
        ]);
    }

   public function show(User $user)
{
    $user->load([
        'orders' => fn($q) => $q->latest()->take(10)
    ]);

    $user->loadCount('orders');

    $totalSpent = $user->orders()->sum('total_amount');

    return Inertia::render('Admin/Customers/Show', [
        'customer' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'role' => $user->role,
            'created_at' => $user->created_at,

            'orders_count' => $user->orders_count,
            'total_spent' => $totalSpent,

            'orders' => $user->orders->map(fn ($order) => [
                'id' => $order->id,
                'total' => $order->total_amount,
                'status' => $order->status,
                'created_at' => $order->created_at,
            ]),
        ]
    ]);
}
}