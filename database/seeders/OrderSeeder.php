<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Coupon::insert([
            [
                'code' => 'GLOW100',
                'type' => 'flat',
                'value' => 100,
                'min_order_amount' => 500,
                'is_active' => true,
                'expires_at' => now()->addMonths(2),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $customer = Customer::first();
        $products = Product::take(2)->get();

        if (! $customer || $products->count() < 2) {
            return;
        }

        $subtotal = $products[0]->price + $products[1]->price;
        $shipping = 60;
        $discount = 100;

        $userId = User::query()
            ->where('email', $customer->email)
            ->value('id');

        $order = Order::create([
            'order_number' => 'ORD-1001',
            'user_id' => $userId,
            'customer_id' => $customer->id,
            'status' => 'packed',
            'payment_status' => 'paid',
            'payment_method' => 'razorpay',
            'delivery_type' => 'express',
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'shipping_charge' => $shipping,
            'total_amount' => $subtotal + $shipping - $discount,
            'notes' => 'Sample seeded order',
        ]);

        foreach ($products as $product) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $product->price,
                'line_total' => $product->price,
            ]);
        }
    }
}
