<div x-data="cartDrawer" @toggle-cart.window="toggle()" class="relative z-50">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/40" @click="open = false" style="display:none;"></div>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed right-0 top-0 flex h-full w-full max-w-md flex-col bg-white shadow-2xl"
        style="display:none;"
    >
        <div class="flex items-center justify-between border-b border-brand-100 px-6 py-4">
            <h2 class="font-serif text-xl font-semibold">Your Cart</h2>
            <button type="button" @click="open = false" class="rounded-full p-2 hover:bg-brand-50" aria-label="Close cart">✕</button>
        </div>

        <div class="flex-1 overflow-y-auto px-6 py-4">
            <template x-if="loading">
                <div class="space-y-4">
                    <div class="skeleton h-20"></div>
                    <div class="skeleton h-20"></div>
                </div>
            </template>
            <template x-if="!loading && items.length === 0">
                <div class="py-12 text-center">
                    <p class="text-slate-600">Your cart is empty.</p>
                    <a href="{{ route('products.index') }}" class="btn-primary mt-4 inline-flex" @click="open = false">Continue Shopping</a>
                </div>
            </template>
            <template x-for="item in items" :key="item.slug">
                <div class="mb-4 flex gap-4 border-b border-brand-50 pb-4">
                    <img :src="item.image" :alt="item.name" class="h-16 w-16 rounded-lg object-cover">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold" x-text="item.name"></p>
                        <p class="text-xs text-slate-500" x-text="'Qty: ' + item.quantity"></p>
                        <p class="text-sm font-bold text-brand-800" x-text="'₹' + (item.price * item.quantity).toLocaleString()"></p>
                    </div>
                    <button
                        type="button"
                        @click="removeItem(item.slug)"
                        :disabled="removing === item.slug"
                        class="self-start text-xs font-medium text-rose-600 hover:text-rose-800 disabled:opacity-50"
                        aria-label="Remove item"
                    >✕</button>
                </div>
            </template>
        </div>

        <div class="border-t border-brand-100 px-6 py-4">
            <div class="mb-4 flex justify-between text-sm">
                <span>Subtotal</span>
                <span class="font-bold" x-text="'₹' + total.toLocaleString()"></span>
            </div>
            <a href="{{ route('cart.index') }}" class="btn-secondary mb-2 w-full text-center">View Cart</a>
            <a href="{{ route('checkout.index') }}" class="btn-primary w-full text-center">Checkout</a>
        </div>
    </div>
</div>
