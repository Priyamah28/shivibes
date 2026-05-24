<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unique(['product_id', 'user_id']);
            });
        } catch (\Throwable) {
            // Index may already exist or duplicate rows prevent creation.
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        try {
            Schema::table('reviews', function (Blueprint $table) {
                $table->dropUnique(['product_id', 'user_id']);
            });
        } catch (\Throwable) {
            //
        }
    }
};
