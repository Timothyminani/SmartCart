<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ProductControllerPublic;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Frontend\PaymentController;
use App\Http\Controllers\CompareController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AiSearchController;
use App\Http\Controllers\Admin\AdminOrderController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\InvoiceController;
// Customize Routes


// Google OAuth Routes

Route::get('/auth/google', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);


//Admin Routes

Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // categories

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    // brands

    Route::get('/brands', [BrandController::class, 'index']);
    Route::post('/brands', [BrandController::class, 'store']);
    Route::put('/brands/{brand}', [BrandController::class, 'update']);
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy']);


 // Products
    Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('admin.products.destroy');

    // Orders
     Route::get('/orders', [AdminOrderController::class, 'index'])
            ->name('admin.orders.index');

    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])
            ->name('admin.orders.show');

    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])
            ->name('admin.orders.update-status');
            

});




//Public Routes

Route::get('/productListing', [ProductControllerPublic::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductControllerPublic::class, 'show'])
    ->name('products.show');

    // Compare Routes
Route::get('/compare', [CompareController::class, 'index'])
    ->name('compare.index');

Route::post('/compare/ai', [CompareController::class, 'generateAi'])
    ->name('compare.ai');

Route::get('/compare/ai/{comparison}', [CompareController::class, 'aiResult'])
    ->name('compare.ai.result');


// AI Search Route
Route::get('/ai-search', function () {
return Inertia::render('Products/Ai/Search');
})->name('ai.search.page');

Route::post('/api/ai-search', [AiSearchController::class, 'search'])
->name('api.ai.search');




    // Cart Routes

    Route::middleware('auth')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'getCart']);   // GET cart
    Route::post('/add', [CartController::class, 'add']);   // ADD item
    Route::post('/update', [CartController::class, 'update']); // UPDATE qty
    Route::post('/remove', [CartController::class, 'remove']); // REMOVE item
    Route::post('/clear', [CartController::class, 'clear']);   // CLEAR cart
    Route::get('/recommendations', [CartController::class, 'recommendations']); // GET recommendations
});



// Chackout Route

Route::middleware(['auth'])->group(function () {

    // 🛒 Checkout page
    Route::get('/checkout', function () {
        return Inertia::render('Checkout');
    })->name('checkout');


Route::post('/checkout', [CheckoutController::class, 'store']);

});


Route::middleware(['auth'])->group(function () {
    Route::get('/order-success/{order}', [OrderController::class, 'success'])
        ->name('order.success');
});

Route::get('/orders', [OrderController::class, 'index'])
    ->middleware('auth')
    ->name('orders.index');

Route::get('/orders/{id}', [OrderController::class, 'show'])
    ->middleware('auth')
    ->name('orders.show');

Route::get('/payment/{payment}', [PaymentController::class, 'show'])
    ->name('payment.page');






    
Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::get('/admin/dashboard', function () {
    return Inertia::render('Admin/Dashboard');
})->middleware(['auth', 'admin']);

Route::get('/dashboard', function () {
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


// Invoice Routes

Route::middleware('auth')->get(
    '/orders/{order}/invoice',
    [InvoiceController::class, 'customer']
)->name('orders.invoice');

Route::middleware(['auth','admin'])
    ->get('/admin/orders/{order}/invoice',
        [InvoiceController::class, 'admin'])
    ->name('admin.orders.invoice');















require __DIR__.'/auth.php';
