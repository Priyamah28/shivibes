@extends('layouts.app')

@section('content')
    <h1 class="mb-2 font-serif text-3xl font-bold text-brand-900">Checkout</h1>
    <p class="mb-6 text-sm text-slate-600">Review your order, choose delivery, and pay securely.</p>

    @if (empty($cart))
        <div class="rounded-2xl border border-dashed border-brand-200 bg-white p-8 text-center text-sm text-slate-600">
            Your cart is empty. <a href="{{ route('products.index') }}" class="font-medium text-brand-700 hover:underline">Browse products</a>.
        </div>
    @else
        <form
            method="POST"
            action="{{ route('checkout.store') }}"
            novalidate
            @submit="submit($event)"
            x-data="checkoutPayment(@js([
                'storeUrl' => route('checkout.store'),
                'verifyUrl' => route('checkout.verify'),
                'csrfToken' => csrf_token(),
                'businessName' => config('app.name', 'Shivibes'),
                'razorpayConfigured' => $razorpayConfigured,
                'codEnabled' => $codEnabled,
                'defaultMode' => $addresses->isNotEmpty() ? 'saved' : 'new',
                'hasSavedAddresses' => $addresses->isNotEmpty(),
                'subtotal' => (float) $subtotal,
            ]))"
        >
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

                            <template x-if="mode === 'saved'">
                                <div class="mt-4 space-y-3">
                                    @foreach ($addresses as $address)
                                        <label class="flex cursor-pointer gap-3 rounded-xl border p-4 {{ $address->is_default ? 'border-gold-400 bg-gold-50/50' : 'border-brand-100 hover:border-brand-300' }}">
                                            <input type="radio" name="customer_address_id" value="{{ $address->id }}" @checked($defaultAddress?->id === $address->id) class="mt-1" required>
                                            <span class="text-sm">
                                                <span class="font-medium">{{ $address->full_name }}</span>
                                                <span class="badge ml-1 bg-brand-100 text-xs">{{ $address->typeLabel() }}</span>
                                                @if ($address->is_default)<span class="badge ml-1 bg-gold-100 text-xs">Default</span>@endif
                                                <span class="mt-1 block whitespace-pre-line text-slate-600">{{ $address->formattedLines() }}</span>
                                                <span class="mt-1 block text-slate-500">{{ $address->phone }}</span>
                                            </span>
                                        </label>
                                    @endforeach
                                    <p class="text-xs text-slate-500"><a href="{{ route('account.addresses.index') }}" class="text-brand-700 hover:underline">Manage addresses</a></p>
                                </div>
                            </template>
                        @endif

                        <template x-if="mode === 'new' || !hasSavedAddresses">
                            <div class="mt-4">
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
                        </template>
                    </section>

                    <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                        <h2 class="font-semibold text-brand-900">Delivery Speed</h2>
                        <select name="delivery_type" required class="input-field mt-3" @change="updateShipping($event.target)">
                            <option value="normal">Normal Delivery (India Post) — Free</option>
                            <option value="express">Express Delivery (Shiprocket) — ₹60</option>
                        </select>
                        <textarea name="notes" rows="2" placeholder="Order notes (optional)" class="input-field mt-3">{{ old('notes') }}</textarea>
                    </section>

                    <section class="rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
                        <h2 class="font-semibold text-brand-900">Payment</h2>
                        <div class="mt-4 space-y-3">
                            <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-brand-200 p-4 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/40">
                                <input type="radio" name="payment_method_choice" value="razorpay" x-model="paymentMethod" class="mt-1" {{ $razorpayConfigured ? '' : 'disabled' }}>
                                <span>
                                    <span class="font-medium text-brand-900">Pay online (Razorpay)</span>
                                    <span class="mt-0.5 block text-xs text-slate-500">UPI, cards, netbanking — secure checkout</span>
                                </span>
                            </label>
                            @if ($codEnabled)
                                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-brand-100 p-4 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/40">
                                    <input type="radio" name="payment_method_choice" value="cod" x-model="paymentMethod" class="mt-1">
                                    <span>
                                        <span class="font-medium text-brand-900">Cash on delivery</span>
                                        <span class="mt-0.5 block text-xs text-slate-500">Pay when your order arrives</span>
                                    </span>
                                </label>
                            @endif
                        </div>
                        @unless ($razorpayConfigured)
                            <p class="mt-3 text-xs text-amber-700">Online payment is not configured on this server. Contact support or use COD if enabled.</p>
                        @endunless
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
                            <div class="flex justify-between"><span>Subtotal</span><span x-text="'₹' + subtotal.toLocaleString('en-IN')"></span></div>
                            <div class="flex justify-between text-slate-600">
                                <span>Shipping</span>
                                <span x-text="shipping ? '₹' + shipping : 'Free'"></span>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-between border-t border-brand-100 pt-4 text-lg font-bold">
                            <span>Total</span>
                            <span class="text-brand-800" x-text="'₹' + total.toLocaleString('en-IN')"></span>
                        </div>
                        <button
                            type="submit"
                            class="btn-primary relative mt-6 w-full justify-center disabled:cursor-not-allowed disabled:opacity-60"
                            :disabled="processing || (paymentMethod === 'razorpay' && !@js($razorpayConfigured))"
                        >
                            <span x-show="!processing" x-text="payLabel"></span>
                            <span x-show="processing" x-cloak class="inline-flex items-center gap-2">
                                <svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Processing…
                            </span>
                        </button>
                        <p class="mt-3 text-center text-xs text-slate-500">
                            <span x-show="paymentMethod === 'razorpay'">You will complete payment in a secure Razorpay window.</span>
                            <span x-show="paymentMethod === 'cod'" x-cloak>Your order will be confirmed; pay on delivery.</span>
                        </p>
                    </div>
                </aside>
            </div>
        </form>
    @endif
@endsection

@push('head')
    @if (! empty($cart) && $razorpayConfigured)
        <script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
    @endif
@endpush
