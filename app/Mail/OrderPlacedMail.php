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

class OrderPlacedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, UsesShivibesMailer;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: $this->shivibesFrom(),
            replyTo: $this->supportReplyTo(),
            subject: 'Order confirmed — '.$this->order->order_number,
        );
    }

    public function content(): Content
    {
        $this->order->loadMissing(['items.product', 'customer']);

        return new Content(
            view: 'mail.html.order-placed',
            with: [
                'order' => $this->order,
                'items' => $this->order->items,
                'deliveryLabel' => $this->deliveryLabel(),
                'trackingPlaceholder' => 'Tracking will be shared once your order ships.',
                'orderUrl' => route('account.orders.show', $this->order),
            ],
        );
    }

    private function deliveryLabel(): string
    {
        return match ($this->order->delivery_type) {
            'express' => 'Express (Shiprocket)',
            default => 'Standard (India Post)',
        };
    }
}
