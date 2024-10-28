<!DOCTYPE html>
<html>

<head>
    <title>Đặt hàng thành công</title>
</head>

<body>
    <h1>Đơn hàng của bạn đã được tạo!</h1>
    <p><strong>Tên khách hàng:</strong> {{ $order->lastname . ' ' . $order->firstname }}</p>
    <p><strong>Số điện thoại:</strong> {{ $order->customer_phone }}</p>
    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->status }}</p>
</body>
</html>