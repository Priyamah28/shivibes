<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderStatusService
{
    public function __construct(
        private readonly MailService $mailService,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Order $order, array $data, ?User $actor = null): Order
    {
        return DB::transaction(function () use ($order, $data, $actor) {
            $previousStatus = $order->status;
            $previousPayment = $order->payment_status;

            if (isset($data['status'])) {
                $order->status = $data['status'];

                if ($data['status'] === Order::STATUS_SHIPPED && ! $order->shipped_at) {
                    $order->shipped_at = $data['shipped_at'] ?? now();
                }

                if ($data['status'] === Order::STATUS_DELIVERED && ! $order->delivered_at) {
                    $order->delivered_at = $data['delivered_at'] ?? now();
                }
            }

            if (isset($data['payment_status'])) {
                $order->payment_status = $data['payment_status'];
            }

            if (array_key_exists('courier_partner', $data)) {
                $order->courier_partner = $data['courier_partner'];
            }

            if (array_key_exists('tracking_number', $data)) {
                $order->tracking_number = $data['tracking_number'];
            }

            if (array_key_exists('tracking_url', $data)) {
                $order->tracking_url = $data['tracking_url'];
            }

            if (array_key_exists('internal_notes', $data)) {
                $order->internal_notes = $data['internal_notes'];
            }

            if (isset($data['shipped_at'])) {
                $order->shipped_at = $data['shipped_at'];
            }

            if (isset($data['delivered_at'])) {
                $order->delivered_at = $data['delivered_at'];
            }

            $order->save();

            if (
                ($order->status !== $previousStatus || $order->payment_status !== $previousPayment)
                || ! empty($data['note'])
            ) {
                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'note' => $data['note'] ?? null,
                    'created_by' => $actor?->id,
                ]);
            }

            $fresh = $order->fresh(['items.product', 'statusHistories.author', 'user', 'customer']);

            if ($fresh->status !== $previousStatus) {
                $this->mailService->sendOrderStatusUpdatedMail($fresh, $previousStatus);
            }

            return $fresh;
        });
    }

    public function recordPlacement(Order $order, ?User $actor = null): void
    {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'note' => 'Order placed',
            'created_by' => $actor?->id,
        ]);
    }
}
