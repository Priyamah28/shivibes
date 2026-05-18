<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    public const TYPE_HOME = 'home';

    public const TYPE_WORK = 'work';

    public const TYPE_OTHER = 'other';

    public const TYPES = [
        self::TYPE_HOME => 'Home',
        self::TYPE_WORK => 'Work',
        self::TYPE_OTHER => 'Other',
    ];

    protected $fillable = [
        'user_id',
        'full_name',
        'phone',
        'phone_alt',
        'address_line_1',
        'address_line_2',
        'landmark',
        'city',
        'state',
        'country',
        'pincode',
        'address_type',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->address_type] ?? ucfirst($this->address_type);
    }

    /**
     * @return array<string, mixed>
     */
    public function toSnapshot(): array
    {
        return [
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'phone_alt' => $this->phone_alt,
            'address_line_1' => $this->address_line_1,
            'address_line_2' => $this->address_line_2,
            'landmark' => $this->landmark,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'pincode' => $this->pincode,
            'address_type' => $this->address_type,
        ];
    }

    public function formattedLines(): string
    {
        $lines = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->landmark,
            implode(', ', array_filter([$this->city, $this->state, $this->pincode])),
            $this->country,
        ]);

        return implode("\n", $lines);
    }
}
