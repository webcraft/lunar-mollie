<?php

namespace Webcraft\Lunar\Mollie\Tests;

use Cartalyst\Converter\Laravel\ConverterServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use Livewire\LivewireServiceProvider;
use Lunar\LunarServiceProvider;
use Lunar\Tests\Stubs\User;
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
        return [
            LunarServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ActivitylogServiceProvider::class,
            ConverterServiceProvider::class,
            NestedSetServiceProvider::class,
            BlinkServiceProvider::class,
            LivewireServiceProvider::class,
            MollieServiceProvider::class,
            MolliePaymentsServiceProvider::class,
        ];
    }
}
