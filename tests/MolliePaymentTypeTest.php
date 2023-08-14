<?php

use Illuminate\Support\Facades\Http;
use Lunar\Facades\Payments;
use Lunar\Models\Transaction;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Webcraft\Lunar\Mollie\Tests\Utils\CartBuilder;

beforeEach(function () {
    config()->set('lunar.mollie.test_mode', true);
    config()->set('lunar.mollie.test_key', 'test_987654321098765432109876543210');
});

function initiatePayment(string $expectedPaymentId): \Lunar\Models\Cart
{
    $cart = CartBuilder::build();

    $client = app(MollieApiClient::class);
    $expectedPayment = new Payment($client);
    $expectedPayment->id = $expectedPaymentId;
    $expectedPayment->description = 'Order description';
    $expectedPayment->_links = [
        'checkout' => [
            'href' => 'https://mollie.com/here_goes_molllies_checkout_url',
        ],
    ];

    Http::fake([
        'https://api.mollie.com/*/payments' => Http::response(json_encode($expectedPayment)),
    ]);

    Payments::driver('mollie')->cart($cart)->withData([
        'description' => trans('lunar::mollie.payment_description'),
        'redirectRoute' => config('lunar.mollie.redirect_route'),
        'webhookUrl' => route(config('lunar.mollie.webhook_route')),
    ])->initiatePayment();

    return $cart;
}

it('creates a transaction on initiate payment', function () {
    $expectedPaymentId = uniqid('tr_');

    $cart = initiatePayment($expectedPaymentId);

    $transaction = Transaction::where('order_id', $cart->draftOrder->id)->first()->getRawOriginal();

    expect($transaction)->toMatchArray([
        'success' => false,
        'driver' => 'mollie',
        'order_id' => $cart->id,
        'type' => 'capture',
        'amount' => $cart->total->value,
        'reference' => $expectedPaymentId,
        'status' => 'open',
        'notes' => 'Order description',
    ]);
});

it('updates the transaction on authorize', function() {
    $paymentId = uniqid('tr_');

    $cart = initiatePayment($paymentId);
    $orderId = $cart->draftOrder->id;

    $transaction = Transaction::where('order_id', $orderId)->first();

    $mollieResponse = [
        'resource' => 'payment',
        'id' => $paymentId,
        'mode' => 'test',
        'amount' => [
            'value' => '9.99',
            'currency' => 'EUR'
        ],
        'description' => 'Order description',
        'method' => 'bancontact',
        'status' => 'paid',
        'createdAt' => now(),
        'paidAt' => now(),
        'metadata' => [
            'order_id' => $orderId
        ],
    ];

    Http::fake([
        'https://api.mollie.com/*/payments/'. $paymentId => Http::response(json_encode($mollieResponse)),
    ]);

    Payments::driver('mollie')->withData([
        'paymentId' => $transaction->reference,
    ])->authorize();

    $transaction = Transaction::where('order_id', $orderId)->first()->getRawOriginal();

    expect($transaction)->toMatchArray([
        'success' => true,
        'status' => 'paid',
        'card_type' => 'bancontact',
    ]);
});
