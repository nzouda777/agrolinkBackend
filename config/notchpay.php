<?php

return [
    'base_url' => env('NOTCHPAY_BASE_URL', 'https://api.notchpay.com'),
    'api_key' => env('NOTCHPAY_API_KEY'),
    'webhook_secret' => env('NOTCHPAY_WEBHOOK_SECRET'),
    'currency' => env('NOTCHPAY_CURRENCY', 'XAF'),
    'supported_payment_methods' => [
        'mobile_money',
        'card',
        'bank_transfer',
    ],
];
