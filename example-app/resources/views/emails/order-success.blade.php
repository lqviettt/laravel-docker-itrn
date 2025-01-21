<!DOCTYPE html>
<html>

<head>
    <title>Đặt hàng thành công</title>
</head>

<body>
    <h1>Đơn hàng của bạn đã được tạo thành công!</h1>
    <p>Mã đơn hàng: {{ $order->code }}</p>
    <p>Tên khách hàng: {{ $order->lastname }} {{ $order->firstname }}</p>
    <p>Số điện thoại: {{ $order->customer_phone }}</p>
    <p>Trạng thái đơn hàng: {{ $order->status }}</p>
    <p>Địa chỉ giao hàng: {{ $order->shipping_address_detail }}, {{ $order->shipping_ward }},
        {{ $order->shipping_district }}, {{ $order->shipping_province }}</p>

    <h3>Chi tiết đơn hàng:</h3>
    <ul>
        @foreach ($order->products as $product)
            <li>
                <strong>{{ $product->name }}</strong>
                <br>
                <!-- Hiển thị các tùy chọn (variants) nếu có -->
                @if ($order->product_variants && $order->product_variants->isNotEmpty())
                    <strong>Màu sắc:</strong>
                    @foreach ($order->product_variants as $variant)
                        {{ $variant->value }}<br>
                    @endforeach
                @else
                    <strong>Màu sắc:</strong> Không có
                @endif
                <br>
                <strong>Số lượng:</strong> {{ $product->pivot->quantity }}
                <br>
                <strong>Giá:</strong> {{ number_format($product->price * 1000, 0, ',', '.') }} VND
                <br>
                <strong>Subtotal:</strong>
                {{ number_format($product->pivot->quantity * $product->price * 1000, 0, ',', '.') }} VND
        @endforeach
        <br>
        <strong>Phí vận chuyển:</strong>
        {{ number_format($order->shipping_fee, 0, ',', '.') }} VND
        </li>
    </ul>

    <h3>Total Price:
        {{ number_format($order->total_price, 0, ',', '.') . ' VND' }}
    </h3>
</body>

</html>
