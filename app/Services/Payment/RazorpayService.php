<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;
use Throwable;

class RazorpayService
{
    private ?Api $api = null;

    public function isConfigured(): bool
    {
        return filled(config('razorpay.key')) && filled(config('razorpay.secret'));
    }

    public function publicKey(): ?string
    {
        return config('razorpay.key');
    }

    /**
     * @return array{id: string, amount: int, currency: string, receipt: string}
     */
    public function createOrder(Order $order): array
    {
        $amountPaise = $this->amountInPaise($order->total_amount);

        $payload = [
            'receipt' => $order->order_number,
            'amount' => $amountPaise,
            'currency' => config('razorpay.currency', 'INR'),
            'notes' => [
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ],
        ];

        $razorpayOrder = $this->client()->order->create($payload);

        Log::info('Razorpay order created', [
            'order_id' => $order->id,
            'razorpay_order_id' => $razorpayOrder['id'] ?? null,
            'amount_paise' => $amountPaise,
        ]);

        return [
            'id' => $razorpayOrder['id'],
            'amount' => (int) $razorpayOrder['amount'],
            'currency' => $razorpayOrder['currency'],
            'receipt' => $razorpayOrder['receipt'],
        ];
    }

    public function verifyPaymentSignature(
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $signature
    ): bool {
        try {
            $this->client()->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $signature,
            ]);

            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay payment signature verification failed', [
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function verifyWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = config('razorpay.webhook_secret');

        if (! filled($secret) || ! filled($signature)) {
            Log::warning('Razorpay webhook rejected: missing secret or signature header');

            return false;
        }

        try {
            $this->client()->utility->verifyWebhookSignature($payload, $signature, $secret);

            return true;
        } catch (SignatureVerificationError $e) {
            Log::warning('Razorpay webhook signature verification failed', [
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    public function fetchPayment(string $paymentId): ?array
    {
        try {
            $payment = $this->client()->payment->fetch($paymentId);

            return $payment->toArray();
        } catch (Throwable $e) {
            Log::error('Failed to fetch Razorpay payment', [
                'payment_id' => $paymentId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function amountInPaise(float|string $amount): int
    {
        return (int) round(((float) $amount) * 100);
    }

    public function amountMatchesOrder(Order $order, int $amountPaise): bool
    {
        return $this->amountInPaise($order->total_amount) === $amountPaise;
    }

    private function client(): Api
    {
        if ($this->api === null) {
            $this->api = new Api(
                (string) config('razorpay.key'),
                (string) config('razorpay.secret')
            );
        }

        return $this->api;
    }
}
