<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): Response
    {
        /*
        |--------------------------------------------------------------------------
        | SAVE RETURN URL
        |--------------------------------------------------------------------------
        |
        | Example:
        | /login?redirect=/products/lenovo-yoga...
        |
        | We save that product URL in Laravel's normal "intended" session.
        |
        */

        $redirect = $request->query('redirect');

        if (
            $redirect &&
            str_starts_with($redirect, '/') &&
            !str_starts_with($redirect, '//')
        ) {
            $request->session()->put(
                'url.intended',
                $redirect
            );
        }

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        if (auth()->user()->role === 'admin') {
            return redirect('/admin/dashboard');
        }


        /*
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
        |
        | Laravel reads session('url.intended').
        |
        | If it exists:
        |     /products/lenovo-yoga...
        |
        | If it doesn't:
        |     /
        |
        */

        return redirect()->intended('/');
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}