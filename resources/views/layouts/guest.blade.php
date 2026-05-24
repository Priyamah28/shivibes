@php
    $heroImages = [
        'login' => asset('images/auth/hero-login.svg'),
        'register' => asset('images/auth/hero-register.svg'),
        'verify' => asset('images/auth/hero-verify.svg'),
        'password' => asset('images/auth/hero-password.svg'),
        'default' => asset('images/auth/hero-default.svg'),
    ];
    $heroImage = $heroImages[$variant] ?? $heroImages['default'];
    $logoPath = file_exists(public_path('images/shivibes-logo.png'))
        ? asset('images/shivibes-logo.png')
        : asset('favicon.png');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} | Shivibes</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 antialiased">
    <div class="flex min-h-screen flex-col lg:flex-row">
        {{-- Brand panel --}}
        <div class="relative hidden overflow-hidden lg:flex lg:w-[48%] xl:w-[52%]">
            <img src="{{ $heroImage }}" alt="" class="absolute inset-0 h-full w-full scale-105 object-cover">
            <div class="absolute inset-0 bg-gradient-to-br from-brand-950/92 via-brand-900/80 to-brand-800/70"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_20%,rgba(207,165,79,0.25),transparent_45%)]"></div>
            <div class="relative z-10 flex w-full flex-col justify-between p-10 xl:p-14">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <img src="{{ $logoPath }}" alt="Shivibes" class="h-14 w-auto rounded-lg bg-white/95 p-1.5 shadow-md ring-1 ring-white/20">
                </a>
                <div class="max-w-lg animate-fade-in">
                    <p class="section-eyebrow text-gold-200/90">Herbal luxury skincare</p>
                    <h1 class="mt-4 font-serif text-4xl font-bold leading-[1.1] text-white xl:text-5xl">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-5 text-base leading-relaxed text-brand-100/95">{{ $subtitle }}</p>
                    @endif
                    <ul class="mt-10 space-y-4">
                        @foreach (['Natural Ayurvedic formulations', 'Secure checkout & order tracking', 'Corporate & festival gifting'] as $item)
                            <li class="flex items-center gap-3 text-sm text-white/90">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/10 text-sm text-gold-300 ring-1 ring-white/15">✓</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                <p class="text-xs tracking-wide text-brand-200/70">© {{ date('Y') }} Shivibes Herbal · Crafted in India</p>
            </div>
        </div>

        {{-- Form panel --}}
        <div class="relative flex flex-1 flex-col justify-center overflow-hidden bg-mesh-auth px-5 py-10 sm:px-8 lg:px-14 xl:px-20">
            <div class="pointer-events-none absolute -right-20 top-20 h-72 w-72 rounded-full bg-brand-200/30 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-16 bottom-10 h-64 w-64 rounded-full bg-gold-200/25 blur-3xl"></div>

            <div class="relative mx-auto w-full max-w-md animate-slide-up">
                <div class="mb-8 lg:hidden">
                    <a href="{{ route('home') }}" class="inline-block">
                        <img src="{{ $logoPath }}" alt="Shivibes" class="h-12 w-auto">
                    </a>
                    <h1 class="mt-3 font-serif text-2xl font-semibold text-slate-900">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $subtitle }}</p>
                    @endif
                </div>

                <div class="auth-card">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-center">
                    <a href="{{ route('home') }}" class="section-link justify-center text-slate-500 hover:text-brand-800">← Back to store</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
