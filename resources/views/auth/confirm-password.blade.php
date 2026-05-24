<x-guest-layout
    title="Confirm password"
    subtitle="Please confirm your password before continuing to this secure area."
    variant="login"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Confirm password</h2>
    </div>

    <p class="mb-5 text-sm text-slate-600">This is a secure area. Re-enter your password to continue.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5" x-data="authForm" @submit="submit">
        @csrf

        <div x-data="passwordField">
            <x-input-label for="password" value="Password" />
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pe-12" ::type="visible ? 'text' : 'password'" name="password" required autocomplete="current-password" />
                <button type="button" @click="toggle()" class="absolute inset-y-0 end-0 flex items-center px-3 text-sm font-medium text-slate-500 hover:text-brand-700" tabindex="-1">
                    <span x-text="visible ? 'Hide' : 'Show'"></span>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center" ::class="{ 'is-loading': loading }" ::disabled="loading">
            <span x-show="!loading">Confirm</span>
            <span x-show="loading" x-cloak>Please wait…</span>
        </x-primary-button>
    </form>
</x-guest-layout>
