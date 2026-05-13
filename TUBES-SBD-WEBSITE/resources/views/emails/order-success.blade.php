<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f5f5f5; padding:20px;">

    <div style="max-width:600px; margin:auto; background:white; padding:20px; border-radius:10px;">
        
        <h2 style="color:green;">Payment Successful 🎉</h2>

        <p>Hi,</p>

        <p>Your booking has been confirmed. Here are your details:</p>

        <hr>

        <p><strong>Order Code:</strong> {{ $order->order_code }}</p>
        <p><strong>Total:</strong> ${{ number_format($order->total_amount, 2) }}</p>

        <hr>

        @if($billing)
            <h3>Billing Address:</h3>
            <p style="margin:0;">{{ $billing['first_name'] }} {{ $billing['last_name'] }}</p>
            <p style="margin:0;">{{ $billing['address'] }}</p>
            <p style="margin:0;">{{ $billing['city'] }}, {{ $billing['state'] }}</p>
            <p style="margin:0;">{{ $billing['postal_code'] }}, {{ $billing['country'] }}</p>
            <hr>
        @endif

        <h3>Your Tickets:</h3>

        @foreach($order->tickets as $ticket)
            <div style="border:1px solid #ddd; padding:10px; margin-bottom:10px; border-radius:8px;">
                <p><strong>QR Code:</strong> {{ $ticket->qr_code }}</p>
                <p><strong>Status:</strong> {{ $ticket->status }}</p>
                <p><strong>Type:</strong> {{ $ticket->ticketAvailability->ticketType->name ?? '-' }}</p>
            </div>
        @endforeach

        <hr>

        <p>
            View your order here:
        </p>

        <a href="{{ url('/order/show/' . $order->order_id) }}" 
           style="display:inline-block; padding:10px 15px; background:#000; color:white; text-decoration:none; border-radius:5px;">
            View Order
        </a>

        <p style="margin-top:20px;">Thank you for booking with us 🙌</p>

    </div>

</body>
</html>