<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    private const MAX_LINE_QUANTITY = 99;

    /**
     * Resolve session cart against DB (active products, current price, stock).
     *
     * @param  array<string, array<string, mixed>>  $cart
     * @return Collection<int, array{product: Product, quantity: int, unit_price: float, line_total: float}>
     *
     * @throws ValidationException
     */
    public function resolveCartLines(array $cart): Collection
    {
        if ($cart === []) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $slugs = array_keys($cart);
        sort($slugs);

        $lines = collect();

        foreach ($slugs as $slug) {
            $item = $cart[$slug];
            $quantity = (int) ($item['quantity'] ?? 0);

            if ($quantity < 1) {
                continue;
            }

            $quantity = min($quantity, self::MAX_LINE_QUANTITY);

            /** @var Product|null $product */
            $product = Product::query()
                ->where('slug', $slug)
                ->active()
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'cart' => "“{$slug}” is no longer available. Please update your cart.",
                ]);
            }

            if (! $product->inStock()) {
                throw ValidationException::withMessages([
                    'cart' => "{$product->name} is out of stock.",
                ]);
            }

            if ($product->stock < $quantity) {
                throw ValidationException::withMessages([
                    'cart' => "Only {$product->stock} unit(s) of {$product->name} are available.",
                ]);
            }

            $moq = max(1, (int) $product->moq);
            if ($quantity < $moq) {
                throw ValidationException::withMessages([
                    'cart' => "Minimum order quantity for {$product->name} is {$moq}.",
                ]);
            }

            $unitPrice = (float) $product->price;

            $lines->push([
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => round($unitPrice * $quantity, 2),
            ]);
        }

        if ($lines->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'Your cart is empty.',
            ]);
        }

        return $lines;
    }

    /**
     * Decrement stock for all order items (call when order is confirmed paid / COD placed).
     *
     * @throws ValidationException
     */
    public function decrementForOrder(Order $order): void
    {
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $product = Product::query()
                ->whereKey($item->product_id)
                ->lockForUpdate()
                ->first();

            if (! $product) {
                throw ValidationException::withMessages([
                    'cart' => 'A product on this order is no longer available.',
                ]);
            }

            if ($product->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'cart' => "Insufficient stock for {$product->name}.",
                ]);
            }

            $product->decrement('stock', $item->quantity);
        }
    }

    public function markStockDecremented(Order $order): void
    {
        $order->transaction_meta = array_merge($order->transaction_meta ?? [], [
            'stock_decremented' => true,
            'stock_decremented_at' => now()->toIso8601String(),
        ]);
        $order->save();
    }

    public function stockAlreadyDecremented(Order $order): bool
    {
        return (bool) ($order->transaction_meta['stock_decremented'] ?? false);
    }
}
