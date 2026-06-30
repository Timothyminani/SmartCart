

<h2>Thank you for shopping with SmartCart!</h2>

<p>Hello {{ $order->user->name }},</p>

<p>Your order has been received successfully.</p>

<p><strong>Order Number:</strong> #{{ $order->id }}</p>

<p><strong>Total:</strong> KES {{ number_format($order->total_amount) }}</p>

<p><strong>Delivery Method:</strong>
{{ ucfirst($order->delivery_method) }}</p>

<h3>Ordered Items</h3>

<ul>
@foreach($order->items as $item)
    <li>
        {{ $item->product->name }}
        x {{ $item->quantity }}
    </li>
@endforeach
</ul>

<p>
We'll notify you once your order is processed.
</p>

<p>Thank you,<br>SmartCart Team</p>