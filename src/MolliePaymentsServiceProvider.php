<?php

namespace Webcraft\Lunar\Mollie;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Lunar\Facades\Payments;
use Mollie\Api\MollieApiClient;
use Webcraft\Lunar\Mollie\Components\PaymentForm;
use Webcraft\Lunar\Mollie\Managers\MollieManager;

class MolliePaymentsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        // Register our payment type.
        Payments::extend('mollie', function ($app) {
            return $app->make(MolliePaymentType::class);
        });

        $this->app->singleton(MollieApiClient::class, function ($app) {
            $mollie = new MollieApiClient();
            if (!config('lunar.mollie.test_mode')) {
                $mollie->setApiKey(config('lunar.mollie.live_key'));
            } else {
                $mollie->setApiKey(config('lunar.mollie.test_key'));
            }

            return $mollie;
        });

        Route::group([], function () {
            require __DIR__ . '/../routes/web.php';
        });

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'lunar');

        $this->mergeConfigFrom(__DIR__ . '/../config/mollie.php', 'lunar.mollie');

        $this->publishes([
            __DIR__ . '/../config/mollie.php' => config_path('lunar/mollie.php'),
        ], 'lunar.mollie.config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/lunar'),
        ], 'lunar.mollie.components');

        // Register the mollie payment component.
        Livewire::component('mollie.payment', PaymentForm::class);
    }
}
