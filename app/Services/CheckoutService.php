<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public const PAYMENT_RAZORPAY = 'razorpay';

    public const PAYMENT_COD = 'cod';

    public function __construct(
        private readonly OrderStatusService $orderStatusService,
        private readonly AddressService $addressService,
        private readonly InventoryService $inventoryService,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, array<string, mixed>>  $cart
     */
    public function createPendingOrder(User $user, array $validated, array $cart, string $paymentMethod = self::PAYMENT_RAZORPAY): Order
    {
        return $this->buildOrder($user, $validated, $cart, $paymentMethod, clearCart: false, decrementStock: false);
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, array<string, mixed>>  $cart
     */
    public function placeCodOrder(User $user, array $validated, array $cart, Request $request): Order
    {
        return $this->buildOrder($user, $validated, $cart, self::PAYMENT_COD, clearCart: true, request: $request, decrementStock: true);
    }

    public function attachRazorpayOrder(Order $order, string $razorpayOrderId, array $meta = []): Order
    {
        $order->update([
            'razorpay_order_id' => $razorpayOrderId,
            'transaction_meta' => array_merge($order->transaction_meta ?? [], $meta),
        ]);

        return $order->fresh();
    }

    /**
     * Fulfill inventory when payment is confirmed (Razorpay verify / webhook).
     *
     * @throws ValidationException
     */
    public function fulfillInventoryForPaidOrder(Order $order): void
    {
        if ($this->inventoryService->stockAlreadyDecremented($order)) {
            return;
        }

        DB::transaction(function () use ($order) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($this->inventoryService->stockAlreadyDecremented($locked)) {
                return;
            }

            $this->inventoryService->decrementForOrder($locked);
            $this->inventoryService->markStockDecremented($locked);
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, array<string, mixed>>  $cart
     */
    private function buildOrder(
        User $user,
        array $validated,
        array $cart,
        string $paymentMethod,
        bool $clearCart,
        bool $decrementStock,
        ?Request $request = null,
    ): Order {
        return DB::transaction(function () use ($user, $validated, $cart, $paymentMethod, $clearCart, $decrementStock, $request) {
            $lines = $this->inventoryService->resolveCartLines($cart);
            $address = $this->resolveAddress($user, $validated);

            $subtotal = round((float) $lines->sum('line_total'), 2);
            $shipping = $validated['delivery_type'] === 'express' ? 60 : 0;

            $customer = Customer::create([
                'name' => $address->full_name,
                'email' => $user->email,
                'phone' => $address->phone,
                'address' => $address->formattedLines(),
                'city' => $address->city,
                'state' => $address->state,
                'pincode' => $address->pincode,
            ]);

            $order = Order::create([
                'order_number' => 'ORD-'.now()->format('YmdHis').'-'.strtoupper(substr(uniqid(), -4)),
                'user_id' => $user->id,
                'customer_id' => $customer->id,
                'customer_address_id' => $address->exists ? $address->id : null,
                'shipping_address' => $address->toSnapshot(),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_PENDING,
                'payment_method' => $paymentMethod,
                'delivery_type' => $validated['delivery_type'],
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'shipping_charge' => $shipping,
                'total_amount' => $subtotal + $shipping,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($lines as $line) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $line['product']->id,
                    'quantity' => $line['quantity'],
                    'unit_price' => $line['unit_price'],
                    'line_total' => $line['line_total'],
                ]);
            }

            if ($decrementStock) {
                $this->inventoryService->decrementForOrder($order);
                $this->inventoryService->markStockDecremented($order);
            }

            $this->orderStatusService->recordPlacement($order, $user);

            if ($clearCart && $request) {
                $request->session()->forget('cart');
            }

            return $order;
        });
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function resolveAddress(User $user, array $validated): CustomerAddress
    {
        if (! empty($validated['customer_address_id'])) {
            return $user->addresses()->findOrFail($validated['customer_address_id']);
        }

        $attributes = $this->addressAttributes($validated);

        if ($validated['save_address'] ?? true) {
            $address = $user->addresses()->create($attributes);

            if ($validated['set_as_default'] ?? false) {
                $this->addressService->setDefault($user, $address);
            } elseif (! $user->addresses()->where('is_default', true)->exists()) {
                $address->update(['is_default' => true]);
            }

            return $address;
        }

        $address = new CustomerAddress($attributes);
        $address->user_id = $user->id;

        return $address;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function addressAttributes(array $data): array
    {
        return [
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'phone_alt' => $data['phone_alt'] ?? null,
            'address_line_1' => $data['address_line_1'],
            'address_line_2' => $data['address_line_2'] ?? null,
            'landmark' => $data['landmark'] ?? null,
            'city' => $data['city'],
            'state' => $data['state'],
            'country' => $data['country'] ?? 'India',
            'pincode' => $data['pincode'],
            'address_type' => $data['address_type'] ?? CustomerAddress::TYPE_HOME,
            'is_default' => (bool) ($data['set_as_default'] ?? false),
        ];
    }
}
