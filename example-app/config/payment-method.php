<?php

return [
    'vnpay' => [
        'url' => env('VNPAY_URL'),
        'return_url' => env('VNPAY_RETURN_URL'),
        'refund_url' => env('VNPAY_REFUNDURL'),
        'refund_email' => env('VNPAY_REFUND_EMAIL'),
        'secret_key' => env('VNPAY_HASH_KEY'),
        'tmn_code' => env('VNPAY_TMN_CODE'),
    ]
];
