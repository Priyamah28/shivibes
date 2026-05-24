<x-guest-layout
    title="Choose a new password"
    subtitle="Create a strong password to secure your Shivibes account."
    variant="password"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Set new password</h2>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5" x-data="passwordForm" @submit="handleSubmit" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email address" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-auth.password-fields password-label="New password" confirm-label="Confirm new password" />

        <x-primary-button class="w-full justify-center" ::class="{ 'is-loading': loading }" ::disabled="loading">
            <span x-show="!loading">Update password</span>
            <span x-show="loading" x-cloak>Updating…</span>
        </x-primary-button>
    </form>
</x-guest-layout>
