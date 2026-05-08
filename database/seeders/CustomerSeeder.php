<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::insert([
            [
                'name' => 'Asha Patel',
                'email' => 'asha@example.com',
                'phone' => '9876543210',
                'address' => '12 Green Park Society',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'pincode' => '380015',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rahul Sharma',
                'email' => 'rahul@example.com',
                'phone' => '9898989898',
                'address' => '44 Lake View Apartments',
                'city' => 'Pune',
                'state' => 'Maharashtra',
                'pincode' => '411045',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
