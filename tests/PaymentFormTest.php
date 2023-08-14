<?php

use Illuminate\Support\Facades\Http;
use Livewire\Livewire;
use Mollie\Api\MollieApiClient;
use Mollie\Api\Resources\Payment;
use Webcraft\Lunar\Mollie\Components\PaymentForm;
use Webcraft\Lunar\Mollie\Tests\Utils\CartBuilder;

beforeEach(function () {
    config()->set('lunar.mollie.test_mode', true);
    config()->set('lunar.mollie.test_key', 'test_987654321098765432109876543210');
});

it('displays the basic payment form when no payment methods are specified', function () {
    config()->set('lunar.mollie.specify_payment_methods', false);

    Livewire::test(PaymentForm::class)
        ->assertSee(trans('lunar::mollie.secure_payment_help_text'))
        ->assertSee(trans('lunar::mollie.pay_button'))
        ->assertDontSee(trans('lunar::mollie.pay_with_method_button', ['method' => 'PayPal']));
});

it('displays the payment form with payment methods', function () {
    config()->set('lunar.mollie.specify_payment_methods', true);
    config()->set('lunar.mollie.payment_methods', ['bancontact', 'ideal']);

    Livewire::test(PaymentForm::class)
        ->assertSee(trans('lunar::mollie.pay_with_method_button', ['method' => 'Bancontact']))
        ->assertSee(trans('lunar::mollie.pay_with_method_button', ['method' => 'iDEAL']))
        ->assertDontSee(trans('lunar::mollie.pay_with_method_button', ['method' => 'PayPal']));
});

it('redirects to Mollie payment page when clicking on a payment method', function () {
    config()->set('lunar.mollie.specify_payment_methods', true);
    config()->set('lunar.mollie.payment_methods', ['bancontact', 'ideal']);

    $client = app(MollieApiClient::class);
    $payment = new Payment($client);
    $payment->id = uniqid('tr_');
    $payment->description = 'test';
    $payment->_links = [
        'checkout' => [
            'href' => 'https://mollie.com/here_goes_molllies_checkout_url',
        ],
    ];

    Http::fake([
        'https://api.mollie.com/*' => Http::response(json_encode($payment)),
    ]);

    Livewire::test(PaymentForm::class, ['cart' => CartBuilder::build()])
        ->call('handleSubmit', 'bancontact')
        ->assertRedirect('https://mollie.com/here_goes_molllies_checkout_url');
});

it('redirects to Mollie payment page when clicking on the pay button', function () {
    config()->set('lunar.mollie.specify_payment_methods', false);

    $client = app(MollieApiClient::class);
    $payment = new Payment($client);
    $payment->id = uniqid('tr_');
    $payment->description = 'test';
    $payment->_links = [
        'checkout' => [
            'href' => 'https://mollie.com/here_goes_molllies_checkout_url',
        ],
    ];

    Http::fake([
        'https://api.mollie.com/*' => Http::response(json_encode($payment)),
    ]);

    Livewire::test(PaymentForm::class, ['cart' => CartBuilder::build()])
        ->call('handleSubmit')
        ->assertRedirect('https://mollie.com/here_goes_molllies_checkout_url');
});
