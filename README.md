# Mollie payments driver and Livewire component for Lunar, the Laravel e-commerce package

[![MIT Licensed](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-round)](LICENSE.md)
![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/webcraft/lunar-mollie/tests.yml)

[Lunar](https://lunarphp.io) is a leading package that brings e-commerce functionality to Laravel.

Mollie is a payment provider that offers support for a diverse range of payment methods, such as:
Apple Pay, Bancontact, Bank Transfer, Belfius, Credit Card, Direct Debit, EPS, Gift Card, Giropay, iDEAL, KBC, MyBank, PayPal, Paysafecard, Przelewy24 and Sofort.

This addon provides an implementation of Lunar's AbstractPayment interface for Mollie, and a payment component to be used in your storefront. It is built using Laravel Livewire and Tailwind.

## Requirements

- Lunar >= `0.4`
- A [Mollie](https://mollie.com/) account
- Laravel Livewire (if using frontend components)

## Installation

### Require the composer package

```sh
composer require webcraft/lunar-mollie
```

### Publish the configuration

This will publish the configuration under `config/lunar/mollie.php`.

```bash
php artisan vendor:publish --tag=lunar.mollie.config
```

### Publish the views (optional)

Lunar Mollie comes with a helper component for you to use on your checkout, if you intend to edit the views it provides, you can publish them.

```bash
php artisan vendor:publish --tag=lunar.mollie.components
```

### Publish the translations (optional)

The checkout component uses translations for the buttons, payment methods, etc. If you want to edit these, you can publish them.

```bash
php artisan vendor:publish --tag=lunar.mollie.translations
```

### Enable the driver

Set the driver in `config/lunar/payments.php`

```php
<?php

return [
    // ...
    'types' => [
        'mollie' => [
            'driver' => 'mollie',
        ],
    ],
];
```

### Add your Mollie credentials and other config

Take a look at the configuration in `config/mollie.php`. Where approriate, edit or set the environment variables in your `.env` file. At least the keys will need to be set.

```dotenv
MOLLIE_LIVE_KEY=
MOLLIE_TEST_KEY=
```

> Keys can be found in your Mollie account: https://my.mollie.com/dashboard/developers/api-keys

You can use the `MOLLIE_TEST_MODE` environment variable to switch between live and test mode.

## Storefront Usage

This addon provides a payment component to be used in your storefront. It is built using Laravel Livewire and Tailwind. Make sure these dependencies are installed and configured before continuing.

### Add the payment component

<img src="https://github.com/webcraft/lunar-mollie/assets/56675/a749f324-5750-4b8e-b77c-55c7a2ece47a" alt="Payment component screenshot" width="700">

Wherever you want the payment form to appear, add this component:

```blade
@livewire('mollie.payment', [
    'cart' => $cart,
])
```

If you are using [Lunar's Livewire Starter Kit](https://github.com/lunarphp/livewire-starter-kit), you can add this code to the `payment.blade.php` view, e.g.:

```blade
<div class="bg-white border border-gray-100 rounded-xl">
    <div class="flex items-center h-16 px-6 border-b border-gray-100">
        <h3 class="text-lg font-medium">
            Payment
        </h3>
    </div>

    @if ($currentStep >= $step)
        <div class="p-6 space-y-4">
            @livewire('mollie.payment', [
                'cart' => $cart,
            ])
        </div>
    @endif

</div>
```

By default, the component will just show a Proceed to payment button, redirecting the user to Mollie's hosted payment method selection screen.

If you want the available payment methods to be shown straight from your checkout form, go to `config/lunar/mollie.php`, set `specify_payment_methods` to `true` and uncomment all your available payment methods in `payment_methods`. 
Don't forget these payment methods will need to be enabled in your Mollie account as well.

```php
[
    //...
    
    'specify_payment_methods' => true,

    'payment_methods' => [
        'bancontact',
        'creditcard',
        'ideal',
        'paypal',
    ],
]
```

### Webhooks

Mollie will send a webhook to your application after every payment attempt (whether successful or not). The route and logic for handling this webhook is already implemented.
If you prefer to write your own logic however, you can create a named route for this yourself, and change the `webhook_route` config value to the name of your route.

### Implement the redirect routes

After a payment attempt, Mollie will redirect the user back to your application. By default, the `MollieRedirectController` will handle this redirect and redirect the user to the checkout success or failure pages.
If you want to implement your own logic, you can create a named route for this yourself, and change the `redirect_route` config value to the name of your route.

While the `MollieRedirectController` is already implemented by the package, this is just a pass-through controller that will redirect the user to the checkout success or failure pages.
These status pages are not implemented by the package, since they probably are specific to your theme. There are 4 statuses that need a page: paid, canceled, open and failed. You need to create named routes for these yourself, named `checkout-success.view`, `checkout-canceled.view`, `checkout-open.view` and `checkout-failure.view` respectively. You can change these names in the config if you want to.

```php
[
    //...
    
    'payment_paid_route' => 'checkout-success.view',
    'payment_canceled_route' => 'checkout-canceled.view',
    'payment_open_route' => 'checkout-open.view',
    'payment_failed_route' => 'checkout-failure.view',
]
```

Here is an example of how a component for the checkout success page could look like:

```php
//app/Http/Livewire/CheckoutSuccessPage.php

<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Lunar\Facades\CartSession;
use Lunar\Models\Cart;
use Lunar\Models\Order;

class CheckoutSuccessPage extends Component
{
    public ?Cart $cart;

    public Order $order;

    public function mount()
    {
        $this->cart = CartSession::current();

        if (! $this->cart || ! $this->cart->completedOrder) {
            $this->redirect('/');

            return;
        }
        $this->order = $this->cart->completedOrder;

        CartSession::forget();
    }

    public function render()
    {
        return view('livewire.checkout-success-page');
    }
}
```

```blade
//resources/views/livewire/checkout-success-page.blade.php

<section class="bg-white">
    <div class="max-w-screen-xl px-4 py-32 mx-auto sm:px-6 lg:px-8 lg:py-48">
        <div class="max-w-xl mx-auto text-center">
            <h1 class="mt-8 text-3xl font-extrabold sm:text-5xl">
                <span class="block mt-1 text-blue-500">
                    Thank you for your order
                </span>
            </h1>

            <p class="mt-4 font-medium sm:text-lg">
                Your order reference number is

                <strong>
                    {{ $order->reference }}
                </strong>
            </p>

            <a class="inline-block px-8 py-3 mt-8 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:ring-1 hover:ring-blue-600"
               href="{{ url('/') }}">
                Back Home
            </a>
        </div>
    </div>
</section>

```

```php
//routes/web.php

Route::get('checkout/success', \App\Http\Livewire\CheckoutSuccessPage::class)->name('checkout-success.view');
```

You can do something similar for the other status pages.

## Testing

```bash
composer test
```

## Contributing

Contributions are welcome, if you are thinking of adding a feature, please submit an issue first.

## About Webcraft

[Webcraft](https://www.webcraft.be) is the company of Michiel Loncke, a freelance web developer from Belgium, specialized in building custom web applications and e-commerce solutions using Laravel and Lunar. If you need help with your project, feel free to [get in touch](https://www.webcraft.be/contact). 
