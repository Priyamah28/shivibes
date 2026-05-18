<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reviews') && ! Schema::hasColumn('reviews', 'product_id')) {
            Schema::dropIfExists('reviews');
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('reviewer_name');
                $table->unsignedTinyInteger('rating');
                $table->string('title')->nullable();
                $table->text('body');
                $table->boolean('is_approved')->default(false);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('wishlists') && ! Schema::hasColumn('wishlists', 'user_id')) {
            Schema::dropIfExists('wishlists');
            Schema::create('wishlists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'product_id']);
            });
        }
    }

    public function down(): void
    {
        //
    }
};
