@php
    $otpLength = (int) config('shivibes.otp.length', 6);
@endphp

<x-guest-layout
    title="Verify your email"
    subtitle="We've sent a secure code to your inbox. Enter it below to continue."
    variant="verify"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Enter verification code</h2>
        <p class="mt-2 text-sm text-slate-600">
            Sent to <strong class="text-brand-800">{{ $email }}</strong>
        </p>
    </div>

    <div class="mb-6 lg:hidden text-center">
        <p class="text-sm text-slate-600">Code sent to <strong class="text-brand-800">{{ $email }}</strong></p>
    </div>

    @if (session('status') === 'otp-sent')
        <x-auth.alert type="success">A new verification code has been sent to your email.</x-auth.alert>
    @endif

    @if ($errors->any())
        <x-auth.alert type="error">{{ $errors->first() }}</x-auth.alert>
    @endif

    <form
        method="POST"
        action="{{ route('verification.otp.verify') }}"
        class="space-y-6"
        x-data="otpForm({{ $otpLength }})"
        x-ref="verifyForm"
        @submit="submit"
    >
        @csrf

        <input type="hidden" name="otp" x-ref="otpHidden" value="">

        <div>
            <x-input-label :value="$otpLength . '-digit code'" class="text-center lg:text-left" />
            <div class="mt-3 flex justify-center gap-2 sm:gap-3" @paste="onPaste($event)">
                @for ($i = 0; $i < $otpLength; $i++)
                    <input
                        type="text"
                        inputmode="numeric"
                        maxlength="1"
                        x-ref="digit{{ $i }}"
                        @input="onInput({{ $i }}, $event)"
                        @keydown="onKeydown({{ $i }}, $event)"
                        class="otp-digit"
                        autocomplete="one-time-code"
                        {{ $i === 0 ? 'autofocus' : '' }}
                    >
                @endfor
            </div>
            <x-input-error :messages="$errors->get('otp')" class="mt-3 text-center" />
            <p class="mt-3 text-center text-xs text-slate-500">
                Expires in {{ config('shivibes.otp.expires_minutes', 5) }} minutes
            </p>
        </div>

        <x-primary-button class="w-full justify-center" ::class="{ 'is-loading': loading }" ::disabled="loading">
            <span x-show="!loading">Verify & continue</span>
            <span x-show="loading" x-cloak>Verifying…</span>
        </x-primary-button>
    </form>

    <div class="mt-8 flex flex-col items-center justify-between gap-4 border-t border-brand-100 pt-6 sm:flex-row">
        <form method="POST" action="{{ route('verification.otp.resend') }}">
            @csrf
            <button type="submit" class="text-sm font-semibold text-brand-700 hover:text-brand-900 hover:underline">
                Resend code
            </button>
        </form>

        <form method="POST" action="{{ route('verification.otp.cancel') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-500 hover:text-slate-800 hover:underline">
                Cancel & return to login
            </button>
        </form>
    </div>
</x-guest-layout>
