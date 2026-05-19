<?php

use App\Http\Controllers\Admin\BannerController as AdminBannerController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\CorporateInquiryController as AdminCorporateInquiryController;
use App\Http\Controllers\Admin\TestimonialController as AdminTestimonialController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Account\AddressController as AccountAddressController;
use App\Http\Controllers\Account\OrderController as AccountOrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\StorefrontController;
use App\Http\Controllers\CorporateInquiryController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [StorefrontController::class, 'home'])->name('home');

Route::get('/corporate', [CorporateInquiryController::class, 'create'])->name('corporate.create');
Route::post('/corporate', [CorporateInquiryController::class, 'store'])->name('corporate.store');

Route::post('/newsletter', [NewsletterController::class, 'store'])->name('newsletter.store');
Route::get('/search/suggest', [SearchController::class, 'suggest'])->name('search.suggest');

Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [StorefrontController::class, 'products'])->name('index');
    Route::get('/{slug}', [StorefrontController::class, 'showProduct'])->name('show');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(auth()->user()->defaultRedirectUrl());
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('verified.email')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::get('/cart/summary', [CartController::class, 'summary'])->name('cart.summary');
        Route::post('/cart/add/{slug}', [CartController::class, 'add'])->name('cart.add');
        Route::post('/cart/remove/{slug}', [CartController::class, 'remove'])->name('cart.remove');

        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');
        Route::post('/wishlist/toggle/{slug}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');

        Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

        Route::prefix('account')->name('account.')->group(function () {
            Route::resource('addresses', AccountAddressController::class)->except(['show']);
            Route::patch('addresses/{address}/default', [AccountAddressController::class, 'makeDefault'])->name('addresses.default');

            Route::get('orders', [AccountOrderController::class, 'index'])->name('orders.index');
            Route::get('orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
        });
    });

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        Route::resource('products', AdminProductController::class)->except(['show']);
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::resource('banners', AdminBannerController::class)->except(['show']);
        Route::resource('testimonials', AdminTestimonialController::class)->except(['show']);
        Route::resource('faqs', AdminFaqController::class)->except(['show']);

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::put('/orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');
        Route::get('/inquiries', [AdminCorporateInquiryController::class, 'index'])->name('inquiries.index');
        Route::patch('/inquiries/{inquiry}/status', [AdminCorporateInquiryController::class, 'updateStatus'])->name('inquiries.update_status');

        Route::resource('users', AdminUserController::class)->except(['show', 'destroy']);
        Route::patch('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle_active');
    });

});

Route::get('/fix-server', function () {

    $output = [];

    // try {
    //     Artisan::call('storage:link');
    //     $output[] = 'Storage linked';
    // } catch (\Exception $e) {
    //     $output[] = 'Storage link error: '.$e->getMessage();
    // }

    try {
        Artisan::call('optimize:clear');
        $output[] = 'Optimize cleared';
    } catch (\Exception $e) {
        $output[] = 'Optimize clear error: '.$e->getMessage();
    }

    try {
        Artisan::call('config:clear');
        $output[] = 'Config cleared';
    } catch (\Exception $e) {
        $output[] = 'Config clear error: '.$e->getMessage();
    }

    try {
        Artisan::call('cache:clear');
        $output[] = 'Cache cleared';
    } catch (\Exception $e) {
        $output[] = 'Cache clear error: '.$e->getMessage();
    }

    try {
        Artisan::call('view:clear');
        $output[] = 'View cache cleared';
    } catch (\Exception $e) {
        $output[] = 'View clear error: '.$e->getMessage();
    }

	try {
	        Artisan::call('route:clear');
	        $output[] = 'route cache cleared';
	    } catch (\Exception $e) {
	        $output[] = 'route clear error: '.$e->getMessage();
	    }

    return '<pre>' . implode("\n", $output) . '</pre>';
});
require __DIR__.'/auth.php';
