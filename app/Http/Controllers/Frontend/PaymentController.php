<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function show(Payment $payment)
    {
        return Inertia::render('Frontend/Payment', [
            'payment' => $payment
        ]);
    }
}
