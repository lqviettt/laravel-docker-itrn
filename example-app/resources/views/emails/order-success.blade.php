<!DOCTYPE html>
<html>

<head>
    <title>Đặt hàng thành công</title>
</head>

<body>
    <h1>Đơn hàng của bạn đã được tạo!</h1>
    <p>Mã đơn hàng: {{ $order->code }}</p>
    <p>Tên khách hàng: {{ $order->lastname }} {{ $order->firstname }}</p>
    <p>Số điện thoại: {{ $order->customer_phone }}</p>
    <p>Trạng thái đơn hàng: {{ $order->status }}</p>
    <p>Địa chỉ giao hàng: {{ $order->shipping_address }}</p>

    <h3>Chi tiết đơn hàng:</h3>
    <ul>
        @foreach ($order->products as $product)
        <li>
            {{ $product->name }}
            @if ($product->variants->isNotEmpty())
            @foreach ($product->variants as $variant)
            {{ $variant->value }}
            @endforeach
            @else

            @endif
            - Quantity: {{ $product->pivot->quantity }} - Price: {{ $product->price }}
            <br>
            Subtotal: {{ $product->pivot->quantity * $product->price }}
        </li>
        @endforeach
    </ul>

    <h3>Total Price:
        {{
            number_format($order->products->sum(function ($product) {
                return $product->pivot->quantity * $product->price;
            }), 0, ',', '.') . ' VND';
        }}
    </h3>
</body>

</html>