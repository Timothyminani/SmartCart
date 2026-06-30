<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    /**
     * Customer invoice download
     */
    public function customer(Order $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        return $this->generateInvoice($order);
    }

    /**
     * Admin invoice download
     */
    public function admin(Order $order)
    {
        return $this->generateInvoice($order);
    }

    /**
     * Shared invoice generation
     */
    private function generateInvoice(Order $order)
    {
        $order->load([
            'user',
            'items.product'
        ]);

        $invoiceNumber = 'INV-' .
            str_pad($order->id, 6, '0', STR_PAD_LEFT);

        $pdf = Pdf::loadView('invoices.order', [
            'order' => $order,
            'invoiceNumber' => $invoiceNumber
        ]);

        return $pdf->download(
            $invoiceNumber . '.pdf'
        );
    }
}