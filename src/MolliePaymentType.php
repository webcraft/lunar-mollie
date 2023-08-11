<?php

namespace Webcraft\Lunar\Mollie;

use Lunar\Base\DataTransferObjects\PaymentAuthorize;
use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Base\DataTransferObjects\PaymentRefund;
use Lunar\Models\Order;
use Lunar\Models\Transaction;
use Lunar\PaymentTypes\AbstractPayment;
use Mollie\Api\Exceptions\ApiException;
use Mollie\Api\MollieApiClient;

class MolliePaymentType extends AbstractPayment
{
    protected MollieApiClient $mollie;

    /**
     * Initialise the payment type.
     */
    public function __construct(MollieApiClient $mollie)
    {
        $this->mollie = $mollie;
    }

    public function initiatePayment(): \Mollie\Api\Resources\Payment
    {
        if (!$this->order) {
            if (!$this->order = $this->cart->order) {
                $this->order = $this->cart->createOrder();
            }
        }

        if ($this->order->placed_at) {
            throw new \Exception('Order has already been placed');
        }

        $transaction = Transaction::create([
            'success' => false,
            'driver' => 'mollie',
            'order_id' => $this->order->id,
            'type' => 'capture',
            'amount' => $this->order->total,
            'reference' => '',
            'status' => '',
            'card_type' => '',
        ]);

        $payment = $this->mollie->payments->create([
            "amount" => [
                "currency" => $this->cart->currency->code,
                "value" => number_format($this->order->total->value / pow(10, $this->cart->currency->decimal_places), 2, '.', ''),
            ],
            "description" => str_replace(':order_reference', $this->order->reference, $this->data['description']),
            "redirectUrl" => route($this->data['redirectRoute'], ['order' => $this->order->id, 'transaction' => $transaction->id]),
            "webhookUrl" => $this->data['webhookUrl'],
            "method" => $this->data['method'] ?? null,
            "metadata" => [
                "order_id" => $this->order->id,
            ],
        ]);

        $transaction->update([
            'reference' => $payment->id,
            'status' => $payment->status,
            'notes' => $payment->description,
        ]);

        return $payment;
    }

    /**
     * Authorize the payment for processing.
     *
     * @return \Lunar\Base\DataTransferObjects\PaymentAuthorize
     */
    public function authorize(): PaymentAuthorize
    {
        if (!array_key_exists('paymentId', $this->data)) {
            return new PaymentAuthorize(
                success: false,
                message: json_encode(['status' => 'not_found', 'message' => 'No payment ID provided']),
            );
        }

        $payment = $this->mollie->payments->get($this->data['paymentId']);
        $orderId = $payment->metadata->order_id;

        $transaction = Transaction
            ::where('reference', $this->data['paymentId'])
            ->where('order_id', $orderId)
            ->where('driver', 'mollie')
            ->first();

        $this->order = Order::find($orderId);

        if (!$transaction || !$payment || !$this->order) {
            return new PaymentAuthorize(
                success: false,
                message: json_encode(['status' => 'not_found', 'message' => 'No transaction found for payment ID ' . $this->data['paymentId']]),
            );
        }

        if ($this->order->placed_at) {
            return new PaymentAuthorize(
                success: false,
                message: json_encode(['status' => 'duplicate', 'message' => 'This order has already been placed']),
            );
        }

        if (is_null($payment->amountRefunded) || $payment->amountRefunded->value === '0.00') {
            $transaction->update([
                'success' => $payment->isPaid(),
                'status' => $payment->status,
                'notes' => $payment->description,
                'card_type' => $payment->method ?? '',
                'meta' => [
                    'method' => $payment->method,
                    'locale' => $payment->locale,
                    'details' => $payment->details,
                    'links' => $payment->_links,
                    'countryCode' => $payment->countryCode,
                ],
            ]);
        }

        if ($payment->status === 'paid') {
            $this->order->placed_at = $payment->paidAt;
        }
        $this->order->status = $payment->status === 'paid' ? 'payment-received' : $payment->status; //TODO add mapper
        $this->order->save();

        return new PaymentAuthorize(success: $payment->status === 'paid', message: json_encode(['status' => $payment->status]));
    }

    /**
     * Capture a payment for a transaction.
     *
     * @param \Lunar\Models\Transaction $transaction
     * @param int $amount
     * @return \Lunar\Base\DataTransferObjects\PaymentCapture
     */
    public function capture(Transaction $transaction, $amount = 0): PaymentCapture
    {
        //Policy not implemented for the moment

        return new PaymentCapture(success: true);
    }

    /**
     * Refund a captured transaction
     *
     * @param \Lunar\Models\Transaction $transaction
     * @param int $amount
     * @param string|null $notes
     * @return \Lunar\Base\DataTransferObjects\PaymentRefund
     */
    public function refund(Transaction $transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        try {
            $refund = $this->mollie->paymentRefunds->createForId(
                $transaction->reference,
                [
                    'amount' => [
                        'value' => number_format($amount / 100, 2, '.', ''),
                        'currency' => $transaction->order->currency->code,
                    ],
                    'description' => $notes ?? 'Refund for order ' . $transaction->order->reference,
                ]
            );
        } catch (ApiException $e) {
            return new PaymentRefund(
                success: false,
                message: $e->getMessage()
            );
        }

        $arr = [
            'success' => $refund->status != 'failed',
            'type' => 'refund',
            'driver' => 'mollie',
            'amount' => $refund->amount->value * pow(10, $transaction->order->currency->decimal_places),
            'reference' => $refund->id,
            'status' => $refund->status,
            'notes' => $notes,
            'card_type' => $transaction->card_type,
            'last_four' => $transaction->last_four,
        ];
        $transaction->order->transactions()->create($arr);

        return new PaymentRefund(
            success: true
        );
    }
}
