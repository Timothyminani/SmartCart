

<h2>New Order Received</h2>

<p>A new order has been placed.</p>

<p><strong>Order ID:</strong> #{{ $order->id }}</p>

<p><strong>Customer:</strong> {{ $order->user->name }}</p>

<p><strong>Email:</strong> {{ $order->user->email }}</p>

<p><strong>Total:</strong> KES {{ number_format($order->total_amount) }}</p>

<p><strong>Phone:</strong> {{ $order->phone }}</p>

<p><strong>Address:</strong> {{ $order->address }}</p>