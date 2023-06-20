<p align="center"><img src="https://user-images.githubusercontent.com/1488016/161026191-aab67703-e932-40d0-a4ac-e8bc85fff35e.png" width="300" ></p>


<p align="center">This addon enables Mollie payments on your Lunar storefront.</p>

## Alpha Release

This addon is currently in Alpha, whilst every step is taken to ensure this is working as intended, it will not be considered out of Alpha until more tests have been added and proved.

## Tests required

This package is currently untested. Tests should be added to ensure it's working as intended.

## Requirements

- Lunar >= `0.3`
- A [Mollie](https://mollie.com/) account
- Laravel Livewire (if using frontend components)

## Installation

### Require the composer package

```sh
composer require webcraft/lunar-mollie
```

### Publish the configuration

This will publish the configuration under `config/lunar/mollie.php`.

```sh
php artisan vendor:publish --tag=lunar.mollie.config
```

### Publish the views (optional)

Lunar Mollie comes with some helper components for you to use on your checkout, if you intend to edit the views they provide, you can publish them.

```sh
php artisan vendor:publish --tag=lunar.mollie.components
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

# Storefront Usage

This addon provides some useful components you can use in your Storefront, they are built using Laravel Livewire so bear that in mind.

## Add the payment component

Wherever you want the payment form to appear, add this component:

```blade
@livewire('mollie.payment', [
  'cart' => $cart,
])
```

### Contributing

Contributions are welcome, if you are thinking of adding a feature, please submit an issue first, so we can determine whether it should be included.
