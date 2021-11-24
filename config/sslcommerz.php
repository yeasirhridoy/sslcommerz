<?php

return [
    'url' => [
        'site' => env('APP_URL', 'http://localhost:8000'),
        'api' => env("SSL_COMMERZ_BASE_URL", "https://sandbox.sslcommerz.com"),
        'success' => '/success',
        'failed' => '/fail',
        'cancel' => '/cancel',
        'ipn' => '/ipn',
    ],
    'localhost' => env('SSL_COMMERZ_LOCALHOST', true),
    'store_id' => env('SSL_COMMERZ_STORE_ID'),
    'store_password' => env('SSL_COMMERZ_STORE_PASSWORD'),
    'initiate_url' => '/gwprocess/v4/api.php',
    'validation_url' => '/validator/api/validationserverAPI.php',
    'refund_url' => '/validator/api/merchantTransIDvalidationAPI.php',
    'refund_status_url' => '/validator/api/merchantTransIDvalidationAPI.php',
    'transaction_status_url' => '/validator/api/merchantTransIDvalidationAPI.php',
    'product_category' => 'non-physical',
    'currency' => 'BDT',
    'emi_option' => 0,
    'product_profile' => 'non-physical-goods',
    'address' => 'Dhaka',
    'city' => 'Dhaka',
    'country' => 'Bangladesh',
    'transaction_id_prefix' => '',
    'shipping_method' => 'NO',
];
