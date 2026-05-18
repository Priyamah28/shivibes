@extends('layouts.app')

@section('content')
    <h1 class="mb-2 font-serif text-3xl font-bold text-brand-900">Checkout</h1>
    <p class="mb-6 text-sm text-slate-600">Review your order and choose a delivery address.</p>

    @if (empty($cart))
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white p-8 text-center text-sm text-slate-600">
            Your cart is empty. <a href="{{ route('products.index') }}" class="font-medium text-brand-700 hover:underline">Browse products</a>.
        </div>
    @else
        <form action="{{ route('checkout.store') }}" method="POST" x-data="{ mode: '{{ $addresses->isNotEmpty() ? 'saved' : 'new' }}' }">
            @csrf
            <div class="grid gap-8 lg:grid-cols-5">
                <div class="space-y-6 lg:col-span-3">
                    <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                        <h2 class="font-semibold text-brand-900">Delivery Address</h2>

                        @if ($addresses->isNotEmpty())
                            <div class="mt-4 flex gap-4 text-sm">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="_mode" value="saved" x-model="mode">
                                    Saved address
                                </label>
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" name="_mode" value="new" x-model="mode">
                                    New address
                                </label>
                            </div>

                            <div x-show="mode === 'saved'" class="mt-4 space-y-3">
                                @foreach ($addresses as $address)
                                    <label class="flex cursor-pointer gap-3 rounded-xl border p-4 {{ $address->is_default ? 'border-gold-400 bg-gold-50/50' : 'border-brand-100 hover:border-brand-300' }}">
                                        <input type="radio" name="customer_address_id" value="{{ $address->id }}" @checked($defaultAddress?->id === $address->id) class="mt-1">
                                        <span class="text-sm">
                                            <span class="font-medium">{{ $address->full_name }}</span>
                                            <span class="badge ml-1 bg-brand-100 text-xs">{{ $address->typeLabel() }}</span>
                                            @if ($address->is_default)<span class="badge ml-1 bg-gold-100 text-xs">Default</span>@endif
                                            <span class="mt-1 block text-slate-600 whitespace-pre-line">{{ $address->formattedLines() }}</span>
                                            <span class="mt-1 block text-slate-500">{{ $address->phone }}</span>
                                        </span>
                                    </label>
                                @endforeach
                                <p class="text-xs text-slate-500"><a href="{{ route('account.addresses.index') }}" class="text-brand-700 hover:underline">Manage addresses</a></p>
                            </div>
                        @endif

                        <div x-show="mode === 'new' || {{ $addresses->isEmpty() ? 'true' : 'false' }}" class="mt-4" x-cloak>
                            <x-account.address-form :show-default="true" />
                            <label class="mt-4 flex items-center gap-2 text-sm">
                                <input type="hidden" name="save_address" value="0">
                                <input type="checkbox" name="save_address" value="1" checked class="rounded border-brand-300 text-brand-700">
                                Save this address to my account
                            </label>
                            <label class="mt-2 flex items-center gap-2 text-sm">
                                <input type="hidden" name="set_as_default" value="0">
                                <input type="checkbox" name="set_as_default" value="1" class="rounded border-brand-300 text-brand-700">
                                Set as default address
                            </label>
                        </div>
                    </section>

                    <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                        <h2 class="font-semibold text-brand-900">Delivery Speed</h2>
                        <select name="delivery_type" required class="input-field mt-3">
                            <option value="normal">Normal Delivery (India Post) — Free</option>
                            <option value="express">Express Delivery (Shiprocket) — ₹60</option>
                        </select>
                        <textarea name="notes" rows="2" placeholder="Order notes (optional)" class="input-field mt-3">{{ old('notes') }}</textarea>
                    </section>
                </div>

                <aside class="lg:col-span-2">
                    <div class="sticky top-24 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                        <h2 class="font-semibold text-brand-900">Order Summary</h2>
                        <div class="mt-4 space-y-2 text-sm">
                            @foreach ($cart as $item)
                                <div class="flex justify-between gap-2">
                                    <span class="text-slate-600">{{ $item['name'] }} × {{ $item['quantity'] }}</span>
                                    <span class="font-medium">₹{{ number_format($item['price'] * $item['quantity']) }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 space-y-2 border-t border-brand-100 pt-4 text-sm">
                            <div class="flex justify-between"><span>Subtotal</span><span>₹{{ number_format($subtotal) }}</span></div>
                            <p class="text-xs text-slate-500">Express shipping (+₹60) applied at confirmation if selected.</p>
                        </div>
                        <div class="mt-4 flex justify-between border-t border-brand-100 pt-4 text-lg font-bold">
                            <span>Estimated total</span>
                            <span class="text-brand-800">₹{{ number_format($subtotal) }}+</span>
                        </div>
                        <button type="submit" class="btn-primary mt-6 w-full justify-center">Place Order</button>
                        <p class="mt-3 text-center text-xs text-slate-500">Razorpay payment integration coming soon.</p>
                    </div>
                </aside>
            </div>
        </form>
    @endif
@endsection
