<x-guest-layout
    title="Reset password"
    subtitle="Enter your email and we'll send you a link to choose a new password."
    variant="password"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Forgot password?</h2>
        <p class="mt-1 text-sm text-slate-600"><a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Back to sign in</a></p>
    </div>

    <p class="mb-5 text-sm leading-relaxed text-slate-600 lg:hidden">
        Enter your email and we'll send you a reset link.
    </p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5" x-data="authForm" @submit="submit">
        @csrf

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center" ::class="{ 'is-loading': loading }" ::disabled="loading">
            <span x-show="!loading">Email reset link</span>
            <span x-show="loading" x-cloak>Sending…</span>
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600 lg:hidden">
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Back to sign in</a>
    </p>
</x-guest-layout>
