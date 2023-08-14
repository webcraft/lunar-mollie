<?php

namespace Webcraft\Lunar\Mollie\Controllers;

use Illuminate\Http\Request;
use Lunar\Facades\Payments;

class MollieWebhookController
{
    public function webhook(Request $request)
    {
        $paymentId = $request->input('id');

        Payments::driver('mollie')->withData([
            'paymentId' => $paymentId,
        ])->authorize();

        return response(null, 200);
    }
}
