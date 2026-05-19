<?php

namespace App\Mail;

use App\Mail\Concerns\UsesShivibesMailer;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    public function __construct(
        public Order $order,
        public string $previousStatus,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->shivibesFrom(),
            replyTo: $this->supportReplyTo(),
            subject: 'Order update — '.$this->order->order_number.' is now '.$this->order->statusLabel(),
        );
    }

    public function content(): Content
    {
        $this->order->loadMissing(['items.product', 'customer']);

        return new Content(
            view: 'mail.html.order-status-updated',
            with: [
                'order' => $this->order,
                'previousStatusLabel' => Order::STATUS_LABELS[$this->previousStatus] ?? ucfirst($this->previousStatus),
                'newStatusLabel' => $this->order->statusLabel(),
                'items' => $this->order->items,
                'orderUrl' => route('account.orders.show', $this->order),
                'hasTracking' => filled($this->order->tracking_number),
            ],
        );
    }
}
