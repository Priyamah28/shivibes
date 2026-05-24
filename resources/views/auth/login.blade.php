<x-guest-layout
    title="Welcome back"
    subtitle="Sign in to track orders, save addresses, and checkout faster."
    variant="login"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Sign in</h2>
        <p class="mt-1 text-sm text-slate-600">New here? <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:underline">Create an account</a></p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if (session('error'))
        <x-auth.alert type="error">{{ session('error') }}</x-auth.alert>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="authForm" @submit="submit">
        @csrf

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="passwordField">
            <x-input-label for="password" value="Password" />
            <div class="relative mt-1">
                <x-text-input
                    id="password"
                    class="block w-full pe-12"
                    ::type="visible ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
                <button
                    type="button"
                    @click="toggle()"
                    class="absolute inset-y-0 end-0 flex items-center px-3 text-sm font-medium text-slate-500 hover:text-brand-700"
                    tabindex="-1"
                >
                    <span x-text="visible ? 'Hide' : 'Show'"></span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-600">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                Remember me
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-700 hover:underline">Forgot password?</a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center" ::class="{ 'is-loading': loading }" ::disabled="loading">
            <span x-show="!loading">Sign in</span>
            <span x-show="loading" x-cloak>Signing in…</span>
        </x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600 lg:hidden">
        Don't have an account?
        <a href="{{ route('register') }}" class="font-semibold text-brand-700 hover:underline">Register</a>
    </p>
</x-guest-layout>
