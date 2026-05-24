<x-guest-layout
    title="Verify your email"
    subtitle="We use email verification to keep your account secure."
    variant="verify"
>
    <div class="mb-6 hidden lg:block">
        <h2 class="font-serif text-2xl font-semibold text-brand-900">Check your inbox</h2>
    </div>

    <p class="text-sm leading-relaxed text-slate-600">
        Thanks for signing up! Please verify your email using the link we sent you, or sign in again to receive an OTP code.
    </p>

    @if (session('status') == 'verification-link-sent')
        <x-auth.alert type="success" class="mt-5">
            A new verification link has been sent to your email address.
        </x-auth.alert>
    @endif

    <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>Resend verification email</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-slate-500 hover:text-slate-800 hover:underline">
                Log out
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-sm">
        <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:underline">Return to sign in</a>
    </p>
</x-guest-layout>
