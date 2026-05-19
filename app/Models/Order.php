<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /** @use HasFactory<\Database\Factories\OrderFactory> */
    use HasFactory;
    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_PACKED = 'packed';

    public const STATUS_SHIPPED = 'shipped';

    public const STATUS_OUT_FOR_DELIVERY = 'out_for_delivery';

    public const STATUS_DELIVERED = 'delivered';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUS_RETURNED = 'returned';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PROCESSING,
        self::STATUS_PACKED,
        self::STATUS_SHIPPED,
        self::STATUS_OUT_FOR_DELIVERY,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
        self::STATUS_RETURNED,
    ];

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_PACKED => 'Packed',
        self::STATUS_SHIPPED => 'Shipped',
        self::STATUS_OUT_FOR_DELIVERY => 'Out for Delivery',
        self::STATUS_DELIVERED => 'Delivered',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_RETURNED => 'Returned',
    ];

    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    public const PAYMENT_REFUNDED = 'refunded';

    public const PAYMENT_STATUSES = [
        self::PAYMENT_PENDING,
        self::PAYMENT_PAID,
        self::PAYMENT_FAILED,
        self::PAYMENT_REFUNDED,
    ];

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_id',
        'customer_address_id',
        'status',
        'payment_status',
        'payment_method',
        'delivery_type',
        'subtotal',
        'discount_amount',
        'shipping_charge',
        'total_amount',
        'notes',
        'internal_notes',
        'shipping_address',
        'tracking_number',
        'courier_partner',
        'tracking_url',
        'coupon_code',
        'shipped_at',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'shipping_charge' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'shipping_address' => 'array',
            'shipped_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at');
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function paymentStatusLabel(): string
    {
        return ucfirst($this->payment_status);
    }

    public function isOwnedBy(User $user): bool
    {
        if ($this->user_id !== null) {
            return (int) $this->user_id === (int) $user->id;
        }

        $this->loadMissing('customer');

        return $this->customer !== null
            && strcasecmp($this->customer->email, $user->email) === 0;
    }

    /**
     * @return array<string, mixed>
     */
    public function shippingSnapshot(): array
    {
        return $this->shipping_address ?? [];
    }

    public function shippingFormatted(): string
    {
        $a = $this->shippingSnapshot();
        if ($a === []) {
            return $this->customer?->address ?? '—';
        }

        $lines = array_filter([
            $a['full_name'] ?? null,
            $a['phone'] ?? null,
            $a['address_line_1'] ?? null,
            $a['address_line_2'] ?? null,
            $a['landmark'] ?? null,
            implode(', ', array_filter([
                $a['city'] ?? null,
                $a['state'] ?? null,
                $a['pincode'] ?? null,
            ])),
            $a['country'] ?? null,
        ]);

        return implode("\n", $lines);
    }

    public function statusIndex(): int
    {
        $index = array_search($this->status, self::STATUSES, true);

        return $index === false ? 0 : (int) $index;
    }
}
