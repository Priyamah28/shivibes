<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'description',
        'ingredients',
        'usage_instructions',
        'benefits',
        'price',
        'compare_at_price',
        'stock',
        'image',
        'is_active',
        'product_type',
        'tags',
        'is_featured',
        'is_bestseller',
        'is_trending',
        'moq',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
            'is_bestseller' => 'boolean',
            'is_trending' => 'boolean',
            'tags' => 'array',
        ];
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->where('is_active', true);
    }

    /** Approved reviews visible on the storefront. */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->approved()->latest();
    }

    public function allReviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, ?string $type)
    {
        if ($type) {
            $query->where('product_type', $type);
        }

        return $query;
    }

    public function averageRating(): float
    {
        if ($this->relationLoaded('reviews') && $this->reviews->isNotEmpty()) {
            return round((float) $this->reviews->avg('rating'), 1);
        }

        return round((float) $this->reviews()->avg('rating'), 1);
    }

    public function approvedReviewsCount(): int
    {
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->count();
        }

        return (int) $this->reviews()->count();
    }

    /**
     * @return array<int, int>
     */
    public function ratingBreakdown(): array
    {
        $breakdown = array_fill(1, 5, 0);
        $reviews = $this->relationLoaded('reviews')
            ? $this->reviews
            : $this->reviews()->get(['rating']);

        foreach ($reviews as $review) {
            $rating = (int) $review->rating;
            if ($rating >= 1 && $rating <= 5) {
                $breakdown[$rating]++;
            }
        }

        return $breakdown;
    }

    public function isOnSale(): bool
    {
        return $this->compare_at_price && $this->compare_at_price > $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->isOnSale()) {
            return 0;
        }

        return (int) round((($this->compare_at_price - $this->price) / $this->compare_at_price) * 100);
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public static function defaultImageUrl(): string
    {
        $path = config('shivibes.default_product_image', 'images/product-placeholder.png');

        return asset($path);
    }

    public function primaryImage(): string
    {
        $image = $this->image
            ?? $this->images()->where('is_primary', true)->value('image_path')
            ?? $this->images()->value('image_path');

        if (empty($image)) {
            return self::defaultImageUrl();
        }

        return str_starts_with($image, 'http') ? $image : asset(ltrim($image, '/'));
    }
}
