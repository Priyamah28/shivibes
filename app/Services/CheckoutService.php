<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutService
{
    public function __construct(
        private readonly OrderStatusService $orderStatusService,
        private readonly AddressService $addressService,
    ) {}

    /**
     * @param  array<string, mixed>  $validated
     */
    public function placeOrder(User $user, array $validated, array $cart, Request $request): Order
    {
        return DB::transaction(function () use ($user, $validated, $cart, $request) {
            $address = $this->resolveAddress($user, $validated);

            $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);
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
                'payment_method' => 'razorpay',
                'delivery_type' => $validated['delivery_type'],
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'shipping_charge' => $shipping,
                'total_amount' => $subtotal + $shipping,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($cart as $slug => $item) {
                $product = Product::where('slug', $slug)->first();
                if (! $product) {
                    continue;
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'line_total' => $item['price'] * $item['quantity'],
                ]);
            }

            $this->orderStatusService->recordPlacement($order, $user);

            $request->session()->forget('cart');

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
