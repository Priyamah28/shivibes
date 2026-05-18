<?php

namespace Tests\Feature\Account;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressAndOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_manage_addresses(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('account.addresses.store'), [
                'full_name' => 'Asha Patel',
                'phone' => '9876543210',
                'address_line_1' => '12 Green Park',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'country' => 'India',
                'pincode' => '380015',
                'address_type' => 'home',
                'is_default' => '1',
            ])
            ->assertRedirect(route('account.addresses.index'));

        $this->assertDatabaseHas('customer_addresses', [
            'user_id' => $user->id,
            'full_name' => 'Asha Patel',
            'is_default' => true,
        ]);
    }

    public function test_customer_can_view_own_order_only(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $order = Order::factory()->create(['user_id' => $user->id]);
        Order::factory()->create(['user_id' => $other->id]);

        $this->actingAs($user)
            ->get(route('account.orders.show', $order))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('account.orders.show', Order::where('user_id', $other->id)->first()))
            ->assertForbidden();
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => Order::STATUS_PENDING]);

        $this->actingAs($admin)
            ->put(route('admin.orders.update', $order), [
                'status' => Order::STATUS_CONFIRMED,
                'payment_status' => Order::PAYMENT_PAID,
                'courier_partner' => 'Delhivery',
                'tracking_number' => 'DL123',
                'tracking_url' => 'https://example.com/track/DL123',
            ])
            ->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => Order::STATUS_CONFIRMED,
            'courier_partner' => 'Delhivery',
        ]);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => Order::STATUS_CONFIRMED,
        ]);
    }
}
