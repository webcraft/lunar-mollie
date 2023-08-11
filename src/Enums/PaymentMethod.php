<?php

namespace Webcraft\Lunar\Mollie\Enums;

enum PaymentMethod: string
{
    case APPLEPAY = 'applepay';
    case BANCONTACT = 'bancontact';
    case BANKTRANSFER = 'banktransfer';
    case BELFIUS = 'belfius';
    case CREDITCARD = 'creditcard';
    case DIRECTDEBIT = 'directdebit';
    case EPS = 'eps';
    case GIFTCARD = 'giftcard';
    case GIROPAY = 'giropay';
    case IDEAL = 'ideal';
    case KBC = 'kbc';
    case MYBANK = 'mybank';
    case PAYPAL = 'paypal';
    case PAYSAFECARD = 'paysafecard';
    case PRZELEWY24 = 'przelewy24';
    case SOFORT = 'sofort';

    public function getImageSrc(string $type = 'svg'): ?string
    {
        if ($type === 'size1x') {
            return 'https://mollie.com/external/icons/payment-methods/' . $this->value . '.png';
        } elseif ($type === 'size2x') {
            return 'https://mollie.com/external/icons/payment-methods/' . $this->value . '%402x.png';
        } elseif ($type === 'svg') {
            return 'https://mollie.com/external/icons/payment-methods/' . $this->value . '.svg';
        }

        return null;
    }

    public function getName(?string $locale = null): ?string
    {
        return trans('lunar::mollie.payment_methods.' . $this->value, [], $locale);
    }
}
