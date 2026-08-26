<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\Order;
use App\Models\AdminNotification;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;



class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],

            'flash' => [
            'success' => fn () => $request->session()->get('success'),
            'error' => fn () => $request->session()->get('error'),
        ],

        
        'admin' => [

            'pendingOrdersCount' => auth()->check()
                ? Order::where('status', 'pending')->count()
                : 0,

            'notifications' => auth()->check()
                ? AdminNotification::latest()
                    ->take(5)
                    ->get()
                : [],

            'unreadNotificationsCount' => auth()->check()
                ? AdminNotification::where('is_read', false)
                    ->count()
                : 0,

            'lowStockCount' => auth()->check()
                ? Product::where('stock_quantity', '<=', 5)
                    ->where('stock_quantity', '>', 0)
                    ->count()
                : 0,

        ],


'navigation' => [
    'categories' => Category::query()
        ->select('id', 'name', 'slug')
        ->orderBy('name')
        ->get(),

    'brands' => Brand::query()
        ->select('id', 'name', 'logo')
        ->orderBy('name')
        ->get(),
],


        
        
        ];
    }
}
