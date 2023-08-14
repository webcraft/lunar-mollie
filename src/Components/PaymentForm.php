<?php

namespace Webcraft\Lunar\Mollie\Components;

use Livewire\Component;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;
use Webcraft\Lunar\Mollie\Enums\PaymentMethod;

class PaymentForm extends Component
{
    public Cart $cart;

    public ?PaymentMethod $paymentMethod;

    public function getPaymentMethods(): array
    {
        return array_map(fn($paymentMethod) => PaymentMethod::from($paymentMethod), config('lunar.mollie.payment_methods'));
    }

    public function handleSubmit(?string $paymentMethod = null)
    {
        $payment = Payments::driver('mollie')->cart($this->cart)->withData([
            'description' => trans('lunar::mollie.payment_description'),
            'redirectRoute' => config('lunar.mollie.redirect_route'),
            'webhookUrl' => config('lunar.mollie.override_webhook_url') ?: route(config('lunar.mollie.webhook_route')),
            'method' => $paymentMethod,
        ])->initiatePayment();

        $this->redirect($payment->getCheckoutUrl());
    }

    public function render()
    {
        return view('lunar::mollie.components.payment-form');
    }
}
