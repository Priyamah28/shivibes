<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'razorpay_order_id')) {
                $table->string('razorpay_order_id')->nullable()->after('payment_method');
            }

            if (! Schema::hasColumn('orders', 'razorpay_payment_id')) {
                $table->string('razorpay_payment_id')->nullable()->unique()->after('razorpay_order_id');
            }

            if (! Schema::hasColumn('orders', 'razorpay_signature')) {
                $table->string('razorpay_signature')->nullable()->after('razorpay_payment_id');
            }

            if (! Schema::hasColumn('orders', 'transaction_meta')) {
                $table->json('transaction_meta')->nullable()->after('razorpay_signature');
            }

            if (! Schema::hasColumn('orders', 'payment_verified_at')) {
                $table->timestamp('payment_verified_at')->nullable()->after('transaction_meta');
            }
        });

        if (! Schema::hasTable('razorpay_webhook_events')) {
            Schema::create('razorpay_webhook_events', function (Blueprint $table) {
                $table->id();
                $table->string('event_id')->unique();
                $table->string('event_type');
                $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
                $table->json('payload');
                $table->timestamp('processed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('razorpay_webhook_events');

        Schema::table('orders', function (Blueprint $table) {
            $columns = [
                'razorpay_order_id',
                'razorpay_payment_id',
                'razorpay_signature',
                'transaction_meta',
                'payment_verified_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('orders', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
