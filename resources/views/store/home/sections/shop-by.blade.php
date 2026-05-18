<section class="bg-white py-14">
    <div class="section-container space-y-12">
        <div>
            <x-store.section-header title="Gift by Occasion" subtitle="Find the perfect herbal gift for every celebration." />
            <div class="flex flex-wrap gap-3">
                @foreach (['Diwali', 'Rakhi', 'Wedding', 'Birthday', 'Anniversary', 'Housewarming', 'Thank You'] as $occasion)
                    <a href="{{ route('products.index', ['q' => $occasion]) }}" class="rounded-full border border-brand-200 bg-brand-50 px-5 py-2.5 text-sm font-medium text-brand-800 transition hover:bg-brand-100">{{ $occasion }}</a>
                @endforeach
            </div>
        </div>

        <div>
            <x-store.section-header title="Gift by Recipient" />
            <div class="flex flex-wrap gap-3">
                @foreach (['For Her', 'For Him', 'For Parents', 'For Colleagues', 'For Clients', 'For Teams'] as $recipient)
                    <a href="{{ route('products.index') }}" class="rounded-full border border-gold-200 bg-gold-50 px-5 py-2.5 text-sm font-medium text-gold-800 transition hover:bg-gold-100">{{ $recipient }}</a>
                @endforeach
            </div>
        </div>

        <div>
            <x-store.section-header title="Shop by Budget" />
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([['Under ₹500', 0, 500], ['₹500 – ₹999', 500, 999], ['₹1000 – ₹1999', 1000, 1999], ['₹2000+', 2000, null]] as [$label, $min, $max])
                    <a href="{{ route('products.index', array_filter(['min_price' => $min, 'max_price' => $max])) }}" class="rounded-2xl border border-brand-100 bg-brand-50 p-6 text-center transition hover:border-brand-300 hover:shadow-card">
                        <p class="font-serif text-lg font-semibold text-brand-800">{{ $label }}</p>
                        <p class="mt-1 text-xs text-slate-500">Curated picks</p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</section>
