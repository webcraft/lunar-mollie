<?php

return [
    'test_mode' => env('MOLLIE_TEST_MODE', false),
    'live_key' => env('MOLLIE_LIVE_KEY'),
    'test_key' => env('MOLLIE_TEST_KEY'),

    'payment_description' => 'Order :order_reference',

    'redirect_route' => 'mollie.redirect',
    'webhook_route' => 'mollie.webhook',

    'payment_paid_route' => 'checkout-success.view',
    'payment_canceled_route' => 'checkout-canceled.view',
    'payment_open_route' => 'checkout-open.view',
    'payment_failed_route' => 'checkout-failure.view',
];
