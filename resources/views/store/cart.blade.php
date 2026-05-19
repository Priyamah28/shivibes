@extends('layouts.app')

@section('content')
    <div x-data="cartPage">
        <h1 class="mb-6 font-serif text-2xl font-bold text-slate-900">Your Cart</h1>

        <template x-if="loading">
            <div class="space-y-3">
                <div class="skeleton h-24 rounded-xl"></div>
                <div class="skeleton h-24 rounded-xl"></div>
            </div>
        </template>

        <template x-if="!loading && items.length === 0">
            <div class="rounded-2xl border border-brand-100 bg-white p-10 text-center">
                <p class="text-slate-600">Your cart is empty. Add some herbal products first.</p>
                <a href="{{ route('products.index') }}" class="btn-primary mt-6 inline-flex">Continue Shopping</a>
            </div>
        </template>

        <template x-if="!loading && items.length > 0">
            <div>
                <div class="space-y-3">
                    <template x-for="item in items" :key="item.slug">
                        <div class="flex items-center gap-4 rounded-xl border border-brand-100 bg-white p-4">
                            <img :src="item.image" :alt="item.name" class="h-20 w-20 shrink-0 rounded-lg object-cover">
                            <div class="min-w-0 flex-1">
                                <h2 class="font-semibold text-slate-900" x-text="item.name"></h2>
                                <p class="mt-1 text-sm text-slate-600" x-text="'Qty: ' + item.quantity + ' × ₹' + item.price.toLocaleString()"></p>
                                <p class="mt-1 text-sm font-bold text-brand-800" x-text="'₹' + (item.price * item.quantity).toLocaleString()"></p>
                            </div>
                            <button
                                type="button"
                                @click="removeItem(item.slug)"
                                :disabled="removing === item.slug"
                                class="shrink-0 text-sm font-medium text-rose-600 hover:text-rose-800 disabled:opacity-50"
                            >
                                <span x-show="removing !== item.slug">Remove</span>
                                <span x-show="removing === item.slug" x-cloak>…</span>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="mt-6 flex items-center justify-between rounded-xl border border-brand-100 bg-white p-5">
                    <span class="font-semibold text-slate-800">Total</span>
                    <span class="text-xl font-bold text-brand-800" x-text="'₹' + total.toLocaleString()"></span>
                </div>

                <a href="{{ route('checkout.index') }}" class="btn-primary mt-4 inline-flex w-full justify-center sm:w-auto">
                    Proceed to Checkout
                </a>
            </div>
        </template>
    </div>
@endsection
