<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Services\Dashboard\DashboardAnalyticsService;

class AdminDashboardController extends Controller
{
    public function index(DashboardAnalyticsService $dashboard)
    {
        
        return Inertia::render('Admin/Dashboard', [
           'stats' => $dashboard->stats(),
           'revenueChart' => $dashboard->revenueChart(),
           'orderStatusChart' => $dashboard->orderStatusChart(),
           'categoryChart' => $dashboard->categoryChart(),
           'recentOrders' => $dashboard->recentOrders(),
        ]);
    }
}