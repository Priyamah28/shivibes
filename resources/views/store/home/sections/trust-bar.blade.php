<section class="border-b border-brand-100/80 bg-white/70 py-5 backdrop-blur-sm">
    <div class="section-container">
        <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
            @foreach (['100% Herbal', 'Cruelty Free', 'Pan-India Delivery', 'Secure Checkout', 'Corporate MOQ'] as $label)
                <span class="trust-pill">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-brand-100 text-[10px] text-brand-700">✦</span>
                    {{ $label }}
                </span>
            @endforeach
        </div>
    </div>
</section>
