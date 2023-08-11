<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mollie keys
    |--------------------------------------------------------------------------
    */
    'test_mode' => env('MOLLIE_TEST_MODE', false),
    'live_key' => env('MOLLIE_LIVE_KEY'),
    'test_key' => env('MOLLIE_TEST_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Routes
    |--------------------------------------------------------------------------
    |
    | These are the routes names that will be used to redirect the customer to after
    | the payment has been completed. The default redirect_route and webhook_route
    | are included in the packages routes file, so you don't have to create them
    | yourself. If you want to use your own routes, you can change them here.
    |
    | The redirect_route will be called when the user is redirected back to your
    | website from the Mollie payment screen. Depending on the outcome of the
    | payment attempt, the user will again be redirected to one of the four
    | payment status routes. These routes being part of your theme, they
    | aren't included in the package, be sure to create them yourself.
    */
    'redirect_route' => 'mollie.redirect',
    'webhook_route' => 'mollie.webhook',

    'payment_paid_route' => 'checkout-success.view',
    'payment_canceled_route' => 'checkout-canceled.view',
    'payment_open_route' => 'checkout-open.view',
    'payment_failed_route' => 'checkout-failure.view',

    /*
    |--------------------------------------------------------------------------
    | Payment status mappings
    |--------------------------------------------------------------------------
    |
    | The payment statuses you receive from Mollie will be mapped to the statuses
    | of your orders using the mapping below. Ideally, the values on the right
    | hand side should also be present in your lunar/orders.php config file.
    */

    'payment_status_mappings' => [
        'open' => 'payment-open',
        'canceled' => 'payment-canceled',
        'pending' => 'payment-pending',
        'expired' => 'payment-expired',
        'failed' => 'payment-failed',
        'paid' => 'payment-received',
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment methods
    |--------------------------------------------------------------------------
    |
    | By setting specify_payment_methods to true, you integrate the selection
    | of payment methods into your checkout page. By setting it to false,
    | usres will see Mollie's own payment method selection screen.
    |
    | If you set specify_payment_methods to true, you need to specify every
    | payment method that must be available in the payment_methods array.
    | Make sure these methods are activated in your Mollie dashboard.
    |
    | Possible values: applepay, bancontact, banktransfer, belfius, creditcard,
    | directdebit, eps, giftcard, giropay, ideal, kbc, mybank, paypal,
    | paysafecard, przelewy24, sofort.
    */
    'specify_payment_methods' => false,

    'payment_methods' => [
//        'applepay',
//        'bancontact',
//        'banktransfer',
//        'belfius',
//        'creditcard',
//        'directdebit',
//        'eps',
//        'giftcard',
//        'giropay',
//        'ideal',
//        'kbc',
//        'mybank',
//        'paypal',
//        'paysafecard',
//        'przelewy24',
//        'sofort',
    ],
];
