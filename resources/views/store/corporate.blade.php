@extends('layouts.app')

@section('content')
    <x-store.breadcrumb :items="[['label' => 'Corporate Gifting']]" />

    <div class="mx-auto max-w-4xl">
        <div class="text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brand-600">B2B Gifting</p>
            <h1 class="mt-3 font-serif text-4xl font-bold text-slate-900">Corporate & Bulk Gifting</h1>
            <p class="mx-auto mt-4 max-w-2xl text-slate-600">
                Custom hampers for teams, clients and events. MOQ support, GST invoicing, branding and dedicated account management.
            </p>
        </div>

        <div class="mt-10 grid gap-6 sm:grid-cols-3">
            @foreach ([
                ['title' => 'Bulk Orders', 'desc' => 'Volume pricing from 25+ units'],
                ['title' => 'Custom Branding', 'desc' => 'Logo cards, sleeves & ribbons'],
                ['title' => 'GST Invoicing', 'desc' => 'Company billing with GST number'],
            ] as $feature)
                <article class="rounded-2xl border border-brand-100 bg-white p-5 text-center shadow-sm">
                    <h3 class="font-semibold text-brand-800">{{ $feature['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-600">{{ $feature['desc'] }}</p>
                </article>
            @endforeach
        </div>

        @if (session('success'))
            <div class="mt-8 rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">{{ session('success') }}</div>
        @endif

        <div class="mt-10 rounded-2xl border border-brand-100 bg-white p-8 shadow-sm">
            <h2 class="font-serif text-xl font-semibold">Request a Quote</h2>
            <form action="{{ route('corporate.store') }}" method="POST" class="mt-6 space-y-6">
                @csrf

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">Full Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="input-field">
                        @error('name')<p class="mt-1 text-sm text-rose-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}" class="input-field">
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="input-field">
                    </div>
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium">GST Number</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number') }}" placeholder="22AAAAA0000A1Z5" class="input-field">
                    </div>
                    <div>
                        <label class="block text-sm font-medium">Estimated Quantity</label>
                        <input type="number" name="quantity" value="{{ old('quantity') }}" min="1" placeholder="e.g. 50" class="input-field">
                    </div>
                </div>

                <div class="flex flex-wrap gap-6">
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="needs_branding" value="1" @checked(old('needs_branding')) class="rounded border-brand-300 text-brand-700">
                        Need custom branding
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="callback_requested" value="1" @checked(old('callback_requested')) class="rounded border-brand-300 text-brand-700">
                        Request a callback
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium">Inquiry Details *</label>
                    <textarea name="message" rows="5" required class="input-field" placeholder="Tell us about your event, budget, delivery timeline and product preferences…">{{ old('message') }}</textarea>
                    @error('message')<p class="mt-1 text-sm text-rose-500">{{ $message }}</p>@enderror
                </div>

                <div class="flex flex-wrap gap-4">
                    <button type="submit" class="btn-primary">Submit Inquiry</button>
                    <a href="https://wa.me/919000000000?text=Hi%2C%20I%20need%20corporate%20gifting%20info" target="_blank" class="btn-secondary">WhatsApp Us</a>
                </div>
            </form>
        </div>
    </div>
@endsection
