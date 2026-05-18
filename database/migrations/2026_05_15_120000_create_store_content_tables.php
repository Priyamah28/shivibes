<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->string('image');
            $table->string('placement')->default('home_hero'); // home_hero, promo_strip
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->unsignedTinyInteger('rating')->default(5);
            $table->text('body');
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('general');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamps();
        });

        Schema::table('corporate_inquiries', function (Blueprint $table) {
            $table->string('gst_number')->nullable()->after('company_name');
            $table->unsignedInteger('quantity')->nullable()->after('phone');
            $table->boolean('needs_branding')->default(false)->after('quantity');
            $table->boolean('callback_requested')->default(false)->after('needs_branding');
            $table->string('inquiry_type')->default('corporate')->after('callback_requested'); // corporate, bulk, callback
        });
    }

    public function down(): void
    {
        Schema::table('corporate_inquiries', function (Blueprint $table) {
            $table->dropColumn(['gst_number', 'quantity', 'needs_branding', 'callback_requested', 'inquiry_type']);
        });

        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('testimonials');
        Schema::dropIfExists('banners');
    }
};
