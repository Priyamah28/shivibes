<x-guest-layout
    title="Join Shivibes"
    subtitle="Create your account to shop herbal skincare, track orders, and save favourites."
    variant="register"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Create account</h2>
        <p class="mt-1 text-sm text-slate-600">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Sign in</a></p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5" x-data="passwordForm" @submit="handleSubmit" novalidate>
        @csrf

        <div>
            <x-input-label for="name" value="Full name" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-auth.password-fields />

        <p class="text-xs leading-relaxed text-slate-500">By registering, you agree to receive a one-time email verification code to secure your account.</p>

        <x-primary-button class="w-full justify-center" ::class="{ 'is-loading': loading }" ::disabled="loading">
            <span x-show="!loading">Create account</span>
            <span x-show="loading" x-cloak>Creating account…</span>
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600 lg:hidden">
        Already registered?
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Sign in</a>
    </p>
</x-guest-layout>
