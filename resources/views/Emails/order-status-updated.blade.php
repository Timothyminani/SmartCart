<h2>Hello {{ $order->user->name }},</h2>

<p>Your order <strong>#{{ $order->id }}</strong> has been updated.</p>

<p>
    New Status:
    <strong>{{ ucfirst($order->status) }}</strong>
</p>

<p>
    Payment Status:
    <strong>{{ ucfirst($order->payment_status) }}</strong>
</p>

<p>
    Total Amount:
    <strong>KES {{ number_format($order->total_amount) }}</strong>
</p>

<p>Thank you for shopping with SmartCart.</p>

<p>SmartCart Team</p>