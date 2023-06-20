<?php

use Webcraft\Lunar\Mollie\Controllers\MollieController;

Route::middleware('web')->group(function () {
    Route::get('mollie/redirect/{order}/{transaction}', [MollieController::class, 'redirect'])->name('mollie.redirect');
});

Route::post('mollie/webhook', [MollieController::class, 'webhook'])->name('mollie.webhook');
