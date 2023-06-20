<?php

namespace Webcraft\Lunar\Mollie\Controllers;

use Illuminate\Http\Request;
use Lunar\Facades\Payments;
use Lunar\Models\Order;
use Lunar\Models\Transaction;

class MollieController
{
    public function redirect(Order $order, Transaction $transaction)
    {
        if (!$transaction->reference) {
            return redirect()->route(config('lunar.mollie.payment_failed_route'));
        }

        $paymentAuthorize = Payments::driver('mollie')->withData([
            'paymentId' => $transaction->reference,
        ])->authorize();

        if (!$paymentAuthorize->success) {
            $data = json_decode($paymentAuthorize->message, true);

            return match ($data['status']) {
                'open' => redirect()->route(config('lunar.mollie.payment_open_route')),
                'canceled' => redirect()->route(config('lunar.mollie.payment_canceled_route')),
                default => redirect()->route(config('lunar.mollie.payment_failed_route')),
            };
        }

        return redirect()->route(config('lunar.mollie.payment_paid_route'));
    }

    public function webhook(Request $request)
    {
        $paymentId = $request->input('id');

        Payments::driver('mollie')->withData([
            'paymentId' => $paymentId,
        ])->authorize();

        return response(null, 200);
    }
}
