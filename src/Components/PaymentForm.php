<?php

namespace Webcraft\Lunar\Mollie\Components;

use Livewire\Component;
use Lunar\Facades\Payments;
use Lunar\Models\Cart;

class PaymentForm extends Component
{
    /**
     * The instance of the order.
     *
     * @var Cart
     */
    public Cart $cart;

    public function handleSubmit()
    {
        $payment = Payments::driver('mollie')->cart($this->cart)->withData([
            'description' => config('lunar.mollie.payment_description'),
            'redirectRoute' => config('lunar.mollie.redirect_route'),
            'webhookUrl' => config('lunar.mollie.override_webhook_url') ?: route(config('lunar.mollie.webhook_route')),
        ])->initiatePayment();

        return $this->redirect($payment->getCheckoutUrl(), 303);
    }

    /**
     * {@inheritDoc}
     */
    public function render()
    {
        return view('lunar::mollie.components.payment-form');
    }
}
