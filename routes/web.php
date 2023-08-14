<?php

use Webcraft\Lunar\Mollie\Controllers\MollieRedirectController;
use Webcraft\Lunar\Mollie\Controllers\MollieWebhookController;

Route::middleware('web')->group(function () {
    Route::get('mollie/redirect/{order}/{transaction}', [MollieRedirectController::class, 'redirect'])->name('mollie.redirect');
});

Route::post('mollie/webhook', [MollieWebhookController::class, 'webhook'])->name('mollie.webhook');
