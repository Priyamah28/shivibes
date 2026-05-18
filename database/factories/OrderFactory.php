<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 500, 5000);

        return [
            'order_number' => 'ORD-'.fake()->unique()->numerify('######'),
            'user_id' => User::factory(),
            'customer_id' => Customer::factory(),
            'status' => Order::STATUS_PENDING,
            'payment_status' => Order::PAYMENT_PENDING,
            'payment_method' => 'razorpay',
            'delivery_type' => 'normal',
            'subtotal' => $subtotal,
            'discount_amount' => 0,
            'shipping_charge' => 0,
            'total_amount' => $subtotal,
            'shipping_address' => [
                'full_name' => fake()->name(),
                'phone' => '9876543210',
                'address_line_1' => fake()->streetAddress(),
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'pincode' => '400001',
                'address_type' => 'home',
            ],
        ];
    }
}
