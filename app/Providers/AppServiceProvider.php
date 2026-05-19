<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\CustomerAddress;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Route::bind('address', fn (string $value) => CustomerAddress::where('user_id', auth()->id())
            ->findOrFail($value));

        View::composer('layouts.app', function ($view) {
            $cartCount = (int) collect(session('cart', []))->sum('quantity');
            $wishlistCount = 0;
            $wishlistSlugs = [];

            if (Auth::check() && Schema::hasTable('wishlists')) {
                $wishlistSlugs = Wishlist::query()
                    ->where('user_id', Auth::id())
                    ->join('products', 'products.id', '=', 'wishlists.product_id')
                    ->pluck('products.slug')
                    ->all();
                $wishlistCount = count($wishlistSlugs);
            }

            $view->with([
                'cartCount' => $cartCount,
                'wishlistCount' => $wishlistCount,
                'wishlistSlugs' => $wishlistSlugs,
            ]);

            if (! Schema::hasTable('categories')) {
                $view->with('navCategories', collect());

                return;
            }

            $view->with(
                'navCategories',
                Category::active()->whereNull('parent_id')->orderBy('sort_order')->get()
            );
        });
    }
}
