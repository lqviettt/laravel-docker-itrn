<!DOCTYPE html>
<html>

<head>
    <title>Xác nhận tài khoản</title>
</head>

<body>
    <h1>Chào {{ $user->name }},</h1>
    <p>Cảm ơn bạn đã đăng ký tài khoản.
        Mã xác minh của bạn là: <strong>{{ $user->verification_code }}</strong></p>
</body>

</html>