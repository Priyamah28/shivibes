<section class="bg-white py-14" x-data="{ open: null }">
    <div class="section-container max-w-3xl">
        <x-store.section-header title="Frequently Asked Questions" subtitle="Everything you need to know before you order." class="text-center [&_h2]:mx-auto [&_p]:mx-auto" />
        <div class="mt-8 space-y-3">
            @forelse ($faqs ?? [] as $index => $faq)
                <div class="rounded-xl border border-brand-100 bg-brand-50/30">
                    <button @click="open = open === {{ $index }} ? null : {{ $index }}" class="flex w-full items-center justify-between px-5 py-4 text-left text-sm font-semibold text-slate-900">
                        {{ $faq->question }}
                        <span x-text="open === {{ $index }} ? '−' : '+'"></span>
                    </button>
                    <div x-show="open === {{ $index }}" x-transition class="px-5 pb-4 text-sm text-slate-600" style="display:none;">
                        {{ $faq->answer }}
                    </div>
                </div>
            @empty
                <p class="text-center text-slate-500">FAQs will appear after seeding.</p>
            @endforelse
        </div>
    </div>
</section>
