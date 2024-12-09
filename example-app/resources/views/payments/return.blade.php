<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>VNPAY RESPONSE</title>
    <link href="{{ asset('css/jumbotron-narrow.css') }}" rel="stylesheet">
    <script src="{{ asset('js/jquery-1.11.3.min.js') }}"></script>
</head>

<body>
    <div class="container">
        <div class="header clearfix">
            <h3 class="text-muted">VNPAY RESPONSE</h3>
        </div>
        <div class="table-responsive">
            <div class="form-group">
                <label>Mã đơn hàng:</label>
                <label>{{ $data['vnp_TxnRef'] }}</label>
            </div>
            <div class="form-group">
                <label>Số tiền:</label>
                <label>{{ number_format($data['vnp_Amount'] / 100, 2) }} VND</label>
            </div>
            <div class="form-group">
                <label>Nội dung thanh toán:</label>
                <label>{{ $data['vnp_OrderInfo'] }}</label>
            </div>
            <div class="form-group">
                <label>Mã phản hồi (vnp_ResponseCode):</label>
                <label>{{ $data['vnp_ResponseCode'] }}</label>
            </div>
            <div class="form-group">
                <label>Mã GD Tại VNPAY:</label>
                <label>{{ $data['vnp_TransactionNo'] }}</label>
            </div>
            <div class="form-group">
                <label>Mã Ngân hàng:</label>
                <label>{{ $data['vnp_BankCode'] }}</label>
            </div>
            <div class="form-group">
                <label>Thời gian thanh toán:</label>
                <label>{{ $data['vnp_PayDate'] }}</label>
            </div>
            <div class="form-group">
                <label>Kết quả:</label>
                <label>
                    @if ($isSignatureValid)
                    @if ($isSuccess)
                    <span style="color:blue">GD Thành công</span>
                    @else
                    <span style="color:red">GD Không thành công</span>
                    @endif
                    @else
                    <span style="color:red">Chữ ký không hợp lệ</span>
                    @endif
                </label>
            </div>
        </div>
        <footer class="footer">
            <p>&copy; VNPAY {{ date('Y') }}</p>
        </footer>
    </div>
</body>

</html>