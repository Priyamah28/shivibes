<section class="bg-white py-14">
    <div class="section-container">
        <x-store.section-header title="Why Choose Shivibes" subtitle="Premium experience without the premium markup." />
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ([
                ['title' => 'Clean Herbal Formulas', 'desc' => 'No harsh chemicals — only skin-loving botanical actives.'],
                ['title' => 'Pan-India Delivery', 'desc' => 'India Post for standard, Shiprocket for express shipping.'],
                ['title' => 'Corporate Ready', 'desc' => 'MOQ support, branding and GST invoicing for bulk orders.'],
                ['title' => 'Trusted by Thousands', 'desc' => 'Growing community of happy customers across India.'],
            ] as $item)
                <article class="rounded-2xl border border-brand-100 bg-brand-50/50 p-6">
                    <div class="mb-3 flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 text-white">✦</div>
                    <h3 class="font-semibold text-brand-900">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $item['desc'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>
