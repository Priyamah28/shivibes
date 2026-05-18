<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku')->nullable()->after('slug');
            $table->decimal('compare_at_price', 10, 2)->nullable()->after('price');
            $table->string('product_type')->default('personal')->after('is_active'); // personal, corporate, festival, combo
            $table->json('tags')->nullable()->after('product_type');
            $table->boolean('is_featured')->default(false)->after('tags');
            $table->boolean('is_bestseller')->default(false)->after('is_featured');
            $table->boolean('is_trending')->default(false)->after('is_bestseller');
            $table->unsignedInteger('moq')->default(1)->after('is_trending');
            $table->string('meta_title')->nullable()->after('moq');
            $table->text('meta_description')->nullable()->after('meta_title');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'sku', 'compare_at_price', 'product_type', 'tags',
                'is_featured', 'is_bestseller', 'is_trending', 'moq',
                'meta_title', 'meta_description',
            ]);
        });
    }
};
