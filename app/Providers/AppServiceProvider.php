<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\CustomerAddress;
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
