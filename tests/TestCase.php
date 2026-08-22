<?php

namespace Webcraft\Lunar\Mollie\Tests;

use Cartalyst\Converter\Laravel\ConverterServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Livewire\LivewireServiceProvider;
use Lunar\LunarServiceProvider;
use Lunar\Nestedset\NestedSetServiceProvider as LunarNestedSetServiceProvider;
use Mollie\Laravel\MollieServiceProvider;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelBlink\BlinkServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;
use Webcraft\Lunar\Mollie\MolliePaymentsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        activity()->disableLogging();

        $this->freezeTime();
    }

    protected function getPackageProviders($app)
    {
        $providers = [
            LunarServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ActivitylogServiceProvider::class,
            BlinkServiceProvider::class,
            LivewireServiceProvider::class,
            MollieServiceProvider::class,
            MolliePaymentsServiceProvider::class,
        ];

        // Lunar 1.3 uses Cartalyst Converter and kalnoy/nestedset.
        // Lunar 1.5 dropped Converter and switched to lunarphp/nestedset.
        if (class_exists(ConverterServiceProvider::class)) {
            $providers[] = ConverterServiceProvider::class;
        }

        if (class_exists(NestedSetServiceProvider::class)) {
            $providers[] = NestedSetServiceProvider::class;
        }

        if (class_exists(LunarNestedSetServiceProvider::class)) {
            $providers[] = LunarNestedSetServiceProvider::class;
        }

        return $providers;
    }
}
