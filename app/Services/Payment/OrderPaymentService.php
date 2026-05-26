<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\RazorpayWebhookEvent;
use App\Models\User;
use App\Services\MailService;
use App\Services\OrderStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderPaymentService
{
    public function __construct(
        private readonly RazorpayService $razorpayService,
        private readonly OrderStatusService $orderStatusService,
        private readonly MailService $mailService,
    ) {}

    /**
     * @param  array<string, mixed>  $meta
     * @return array{order: Order, already_paid: bool}
     */
    public function markPaid(
        Order $order,
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $signature,
        array $meta = [],
        ?User $actor = null,
        ?Request $request = null,
    ): array {
        return DB::transaction(function () use ($order, $razorpayOrderId, $razorpayPaymentId, $signature, $meta, $actor, $request) {
            /** @var Order $locked */
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->payment_status === Order::PAYMENT_PAID) {
                return ['order' => $locked->fresh(['items.product', 'user', 'customer']), 'already_paid' => true];
            }

            $this->assertPaymentNotUsedElsewhere($locked, $razorpayPaymentId);

            if (! $this->razorpayService->verifyPaymentSignature($razorpayOrderId, $razorpayPaymentId, $signature)) {
                throw new \RuntimeException('Invalid payment signature.');
            }

            if ($locked->razorpay_order_id && $locked->razorpay_order_id !== $razorpayOrderId) {
                throw new \RuntimeException('Razorpay order mismatch.');
            }

            $payment = $this->razorpayService->fetchPayment($razorpayPaymentId);

            if ($payment === null) {
                throw new \RuntimeException('Unable to verify payment with Razorpay.');
            }

            $this->assertPaymentEntityValid($locked, $payment, $razorpayOrderId);

            $this->applyPaidState($locked, $razorpayOrderId, $razorpayPaymentId, $signature, array_merge($meta, [
                'verified_via' => $meta['verified_via'] ?? 'checkout',
                'razorpay_payment_status' => $payment['status'] ?? null,
                'amount_paise' => (int) ($payment['amount'] ?? 0),
            ]), $actor);

            if ($request) {
                $request->session()->forget('cart');
            }

            $fresh = $locked->fresh(['items.product', 'user', 'customer']);

            if (! ($meta['skip_order_email'] ?? false)) {
                $this->mailService->sendOrderPlacedMail($fresh);
            }

            Log::info('Order marked paid', [
                'order_id' => $fresh->id,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);

            return ['order' => $fresh, 'already_paid' => false];
        });
    }

    public function markFailed(Order $order, array $meta = [], ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $meta, $actor) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->payment_status === Order::PAYMENT_PAID) {
                return $locked;
            }

            $locked->payment_status = Order::PAYMENT_FAILED;
            $locked->transaction_meta = array_merge($locked->transaction_meta ?? [], $meta);
            $locked->save();

            $this->orderStatusService->update($locked, [
                'payment_status' => Order::PAYMENT_FAILED,
                'note' => $meta['note'] ?? 'Payment failed',
            ], $actor);

            Log::info('Order payment marked failed', ['order_id' => $locked->id]);

            return $locked->fresh();
        });
    }

    public function markRefunded(Order $order, array $meta = [], ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $meta, $actor) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            $locked->payment_status = Order::PAYMENT_REFUNDED;
            $locked->transaction_meta = array_merge($locked->transaction_meta ?? [], $meta);
            $locked->save();

            $this->orderStatusService->update($locked, [
                'payment_status' => Order::PAYMENT_REFUNDED,
                'note' => $meta['note'] ?? 'Payment refunded',
            ], $actor);

            Log::info('Order payment marked refunded', ['order_id' => $locked->id]);

            return $locked->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function processWebhookEvent(string $eventId, string $eventType, array $payload): void
    {
        $event = RazorpayWebhookEvent::query()->where('event_id', $eventId)->first();

        if ($event?->processed_at) {
            Log::info('Razorpay webhook already processed', ['event_id' => $eventId]);

            return;
        }

        $event ??= RazorpayWebhookEvent::create([
            'event_id' => $eventId,
            'event_type' => $eventType,
            'payload' => $payload,
        ]);

        try {
            $orderId = match ($eventType) {
                'payment.captured' => $this->handlePaymentCaptured($payload),
                'payment.failed' => $this->handlePaymentFailed($payload),
                'refund.processed' => $this->handleRefundProcessed($payload),
                default => null,
            };

            if ($orderId) {
                $event->order_id = $orderId;
            }

            $event->processed_at = now();
            $event->save();
        } catch (Throwable $e) {
            Log::error('Razorpay webhook processing failed', [
                'event_id' => $eventId,
                'event_type' => $eventType,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handlePaymentCaptured(array $payload): ?int
    {
        $payment = $this->paymentEntityFromPayload($payload);

        if ($payment === null) {
            return null;
        }

        $order = $this->resolveOrderFromPaymentEntity($payment);

        if (! $order) {
            Log::warning('Razorpay payment.captured: order not found', [
                'payment_id' => $payment['id'] ?? null,
            ]);

            return null;
        }

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return $order->id;
        }

        $razorpayOrderId = (string) ($payment['order_id'] ?? $order->razorpay_order_id ?? '');
        $razorpayPaymentId = (string) ($payment['id'] ?? '');

        if ($razorpayOrderId === '' || $razorpayPaymentId === '') {
            return null;
        }

        $amountPaise = (int) ($payment['amount'] ?? 0);

        if (! $this->razorpayService->amountMatchesOrder($order, $amountPaise)) {
            Log::error('Webhook amount mismatch', ['order_id' => $order->id]);

            return null;
        }

        DB::transaction(function () use ($order, $razorpayOrderId, $razorpayPaymentId, $payment) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->payment_status === Order::PAYMENT_PAID) {
                return;
            }

            $this->assertPaymentNotUsedElsewhere($locked, $razorpayPaymentId);

            $this->applyPaidState($locked, $razorpayOrderId, $razorpayPaymentId, null, [
                'verified_via' => 'webhook',
                'razorpay_payment_status' => $payment['status'] ?? 'captured',
                'amount_paise' => (int) ($payment['amount'] ?? 0),
            ]);

            $fresh = $locked->fresh(['items.product', 'user', 'customer']);
            $this->mailService->sendOrderPlacedMail($fresh);
        });

        return $order->id;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handlePaymentFailed(array $payload): ?int
    {
        $payment = $this->paymentEntityFromPayload($payload);

        if ($payment === null) {
            return null;
        }

        $order = $this->resolveOrderFromPaymentEntity($payment);

        if (! $order || $order->payment_status === Order::PAYMENT_PAID) {
            return $order?->id;
        }

        $this->markFailed($order, [
            'verified_via' => 'webhook',
            'razorpay_payment_id' => $payment['id'] ?? null,
            'error' => $payment['error_description'] ?? null,
            'note' => 'Payment failed (webhook)',
        ]);

        return $order->id;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function handleRefundProcessed(array $payload): ?int
    {
        $refund = $payload['payload']['refund']['entity'] ?? $payload['refund']['entity'] ?? null;

        if (! is_array($refund)) {
            return null;
        }

        $paymentId = (string) ($refund['payment_id'] ?? '');
        $order = Order::query()->where('razorpay_payment_id', $paymentId)->first();

        if (! $order) {
            return null;
        }

        $this->markRefunded($order, [
            'verified_via' => 'webhook',
            'refund_id' => $refund['id'] ?? null,
            'note' => 'Refund processed (webhook)',
        ]);

        return $order->id;
    }

    /**
     * @param  array<string, mixed>  $payment
     */
    private function assertPaymentEntityValid(Order $order, array $payment, string $razorpayOrderId): void
    {
        $amountPaise = (int) ($payment['amount'] ?? 0);
        $status = (string) ($payment['status'] ?? '');
        $paymentOrderId = (string) ($payment['order_id'] ?? '');

        if ($paymentOrderId !== '' && $paymentOrderId !== $razorpayOrderId) {
            throw new \RuntimeException('Payment does not belong to this Razorpay order.');
        }

        if (! $this->razorpayService->amountMatchesOrder($order, $amountPaise)) {
            Log::error('Razorpay amount mismatch', [
                'order_id' => $order->id,
                'expected_paise' => $this->razorpayService->amountInPaise($order->total_amount),
                'paid_paise' => $amountPaise,
            ]);

            throw new \RuntimeException('Payment amount does not match order total.');
        }

        if (! in_array($status, ['captured', 'authorized'], true)) {
            throw new \RuntimeException('Payment is not completed.');
        }
    }

    private function assertPaymentNotUsedElsewhere(Order $order, string $razorpayPaymentId): void
    {
        if ($order->razorpay_payment_id && $order->razorpay_payment_id !== $razorpayPaymentId) {
            throw new \RuntimeException('This order already has a payment recorded.');
        }

        $duplicate = Order::query()
            ->where('razorpay_payment_id', $razorpayPaymentId)
            ->where('id', '!=', $order->id)
            ->where('payment_status', Order::PAYMENT_PAID)
            ->exists();

        if ($duplicate) {
            throw new \RuntimeException('Payment already applied to another order.');
        }
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    private function applyPaidState(
        Order $order,
        string $razorpayOrderId,
        string $razorpayPaymentId,
        ?string $signature,
        array $meta,
        ?User $actor = null,
    ): void {
        $order->razorpay_order_id = $razorpayOrderId;
        $order->razorpay_payment_id = $razorpayPaymentId;

        if ($signature) {
            $order->razorpay_signature = $signature;
        }

        $order->payment_status = Order::PAYMENT_PAID;
        $order->payment_method = 'razorpay';
        $order->payment_verified_at = now();
        $order->transaction_meta = array_merge($order->transaction_meta ?? [], $meta);

        if ($order->status === Order::STATUS_PENDING) {
            $order->status = Order::STATUS_CONFIRMED;
        }

        $order->save();

        $this->orderStatusService->update($order, [
            'payment_status' => Order::PAYMENT_PAID,
            'status' => $order->status,
            'note' => $meta['note'] ?? 'Payment received via Razorpay',
        ], $actor);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>|null
     */
    private function paymentEntityFromPayload(array $payload): ?array
    {
        $payment = $payload['payload']['payment']['entity'] ?? $payload['payment']['entity'] ?? null;

        return is_array($payment) ? $payment : null;
    }

    /**
     * @param  array<string, mixed>  $payment
     */
    private function resolveOrderFromPaymentEntity(array $payment): ?Order
    {
        $notes = $payment['notes'] ?? [];

        if (! empty($notes['order_id'])) {
            $order = Order::find($notes['order_id']);
            if ($order) {
                return $order;
            }
        }

        $razorpayOrderId = (string) ($payment['order_id'] ?? '');

        if ($razorpayOrderId !== '') {
            return Order::query()->where('razorpay_order_id', $razorpayOrderId)->first();
        }

        return null;
    }
}
