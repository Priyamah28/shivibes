<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'reviewer_name',
        'rating',
        'title',
        'body',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('is_approved', true);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('is_approved', false);
    }

    public function displayName(): string
    {
        $parts = preg_split('/\s+/', trim($this->reviewer_name), 2);

        if (count($parts) < 2) {
            return $parts[0] ?? 'Customer';
        }

        return $parts[0].' '.mb_substr($parts[1], 0, 1).'.';
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->reviewer_name));

        return strtoupper(
            collect($parts)->take(2)->map(fn (string $p) => mb_substr($p, 0, 1))->implode('')
        ) ?: 'SV';
    }
}
