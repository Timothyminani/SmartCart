<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>{{ $invoiceNumber }}</title>

    <style>

        body{
            font-family: DejaVu Sans, sans-serif;
            color:#333;
            font-size:14px;
            line-height:1.5;
        }

        .header{
            border-bottom:2px solid #2563eb;
            padding-bottom:20px;
            margin-bottom:30px;
        }

        .logo{
            font-size:32px;
            font-weight:bold;
            color:#2563eb;
        }

        .company-details{
            margin-top:8px;
            color:#666;
        }

        .section{
            margin-bottom:30px;
        }

        .section-title{
            font-size:18px;
            font-weight:bold;
            margin-bottom:10px;
            color:#111827;
        }

        .row{
            width:100%;
            overflow:hidden;
        }

        .col{
            width:48%;
            float:left;
        }

        .right{
            float:right;
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:10px;
        }

        table th{
            background:#2563eb;
            color:white;
            padding:12px;
            text-align:left;
        }

        table td{
            border:1px solid #ddd;
            padding:10px;
        }

        .summary{
            margin-top:20px;
            width:40%;
            float:right;
        }

        .summary table td{
            border:none;
        }

        .grand-total{
            font-size:16px;
            font-weight:bold;
            border-top:2px solid #2563eb;
        }

        .badge{
            display:inline-block;
            padding:4px 10px;
            border-radius:4px;
            background:#f3f4f6;
        }

        .footer{
            margin-top:80px;
            text-align:center;
            color:#777;
            border-top:1px solid #ddd;
            padding-top:20px;
        }

    </style>
</head>

<body>

    <!-- HEADER -->

    <div class="header">

        <div class="logo">
            SmartCart
        </div>

        <div class="company-details">
            Nairobi, Kenya <br>
            support@smartcart.com <br>
            +254 769295800
        </div>

    </div>


<!-- INVOICE DETAILS -->

<div class="section">

    <table style="width:100%; border:none;">

        <tr>

            <td style="width:50%; vertical-align:top; border:none;">

                <h3>Invoice Details</h3>

                <p>
                    <strong>Invoice #:</strong>
                    {{ $invoiceNumber }}
                </p>

                <p>
                    <strong>Order #:</strong>
                    {{ $order->id }}
                </p>

                <p>
                    <strong>Date:</strong>
                    {{ $order->created_at->format('d M Y') }}
                </p>

            </td>

            <td style="width:50%; vertical-align:top; border:none;">

                <h3>Customer Information</h3>

                <p>
                    <strong>Name:</strong>
                    {{ $order->user->name }}
                </p>

                <p>
                    <strong>Email:</strong>
                    {{ $order->user->email }}
                </p>

                <p>
                    <strong>Phone:</strong>
                    {{ $order->phone }}
                </p>

                <p>
                    <strong>Address:</strong>
                    {{ $order->address }}
                </p>

            </td>

        </tr>

    </table>

</div>

    <!-- ORDER INFO -->

    <div class="section">

        <h3 class="section-title">
            Order Information
        </h3>

        <table>

            <tr>
                <th>Payment Method</th>
                <th>Payment Status</th>
                <th>Delivery Method</th>
                <th>Order Status</th>
            </tr>

            <tr>
                <td>{{ strtoupper($order->payment_method) }}</td>
                <td>{{ ucfirst($order->payment_status) }}</td>
                <td>{{ ucfirst($order->delivery_method) }}</td>
                <td>{{ ucfirst($order->status) }}</td>
            </tr>

        </table>

    </div>

    <!-- PRODUCTS -->

    <div class="section">

        <h3 class="section-title">
            Ordered Products
        </h3>

        <table>

            <thead>

                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>

            </thead>

            <tbody>

                @foreach($order->items as $item)

                    <tr>

                        <td>
                            {{ $item->product->name }}
                        </td>

                        <td>
                            {{ $item->quantity }}
                        </td>

                        <td>
                            KES {{ number_format($item->price) }}
                        </td>

                        <td>
                            KES {{ number_format($item->price * $item->quantity) }}
                        </td>

                    </tr>

                @endforeach

            </tbody>

        </table>

    </div>

    <!-- SUMMARY -->

    <div class="summary">

        <table>

            <tr>
                <td><strong>Delivery Fee</strong></td>

                <td>
                    KES {{ number_format($order->delivery_fee) }}
                </td>
            </tr>

            <tr class="grand-total">

                <td>
                    <strong>Grand Total</strong>
                </td>

                <td>
                    <strong>
                        KES {{ number_format($order->total_amount) }}
                    </strong>
                </td>

            </tr>

        </table>

    </div>

    <div style="clear:both"></div>

    <!-- FOOTER -->

    <div class="footer">

        Thank you for shopping with SmartCart. <br>

        We appreciate your business.

    </div>

</body>
</html>