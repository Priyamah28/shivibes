<section class="bg-brand-900 py-16 text-white">
    <div class="section-container">
        <x-store.section-header title="Gifting Hub" subtitle="Personal, corporate and festival gifting — all in one place." class="[&_h2]:text-white [&_p]:text-brand-200 [&_a]:text-gold-300" />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                <h3 class="font-serif text-xl font-semibold">Corporate Gifting</h3>
                <p class="mt-2 text-sm text-brand-100">Bulk hampers, branding, GST invoices and dedicated account support.</p>
                <a href="{{ route('corporate.create') }}" class="mt-4 inline-block text-sm font-semibold text-gold-300 hover:text-gold-200">Request catalogue →</a>
                @if (($corporateProducts ?? collect())->isNotEmpty())
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        @foreach ($corporateProducts->take(2) as $product)
                            <x-store.product-card :product="$product" class="!shadow-none" />
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                <h3 class="font-serif text-xl font-semibold">Personal Gifting</h3>
                <p class="mt-2 text-sm text-brand-100">Thoughtful herbal sets for birthdays, anniversaries and everyday care.</p>
                <a href="{{ route('products.index', ['type' => 'personal']) }}" class="mt-4 inline-block text-sm font-semibold text-gold-300">Shop personal →</a>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                <h3 class="font-serif text-xl font-semibold">Festival Collections</h3>
                <p class="mt-2 text-sm text-brand-100">Diwali, Rakhi and seasonal hampers with premium presentation.</p>
                <a href="{{ route('products.index', ['type' => 'festival']) }}" class="mt-4 inline-block text-sm font-semibold text-gold-300">Explore festivals →</a>
            </div>
        </div>

        @if (($comboProducts ?? collect())->isNotEmpty())
            <div class="mt-10">
                <h3 class="mb-4 font-serif text-2xl">Combo Packs — Best Value</h3>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($comboProducts as $product)
                        <x-store.product-card :product="$product" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
