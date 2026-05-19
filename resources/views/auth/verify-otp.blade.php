<x-guest-layout>
    <div class="mb-6 text-center">
        <p class="font-serif text-2xl text-brand-800">Verify your email</p>
        <p class="mt-2 text-sm text-gray-600">
            We sent a 6-digit code to <strong class="text-brand-700">{{ $email }}</strong>.
            Enter it below to access your account.
        </p>
    </div>

    @if (session('status') === 'otp-sent')
        <div class="mb-4 rounded-lg bg-brand-50 px-4 py-3 text-sm font-medium text-brand-800">
            A new verification code has been sent to your email.
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('verification.otp.verify') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="otp" value="Verification code" />
            <x-text-input
                id="otp"
                name="otp"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="{{ config('shivibes.otp.length', 6) }}"
                class="mt-1 block w-full text-center text-2xl tracking-[0.4em] font-mono"
                required
                autofocus
                autocomplete="one-time-code"
                placeholder="000000"
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            <p class="mt-2 text-xs text-gray-500">Code expires in {{ config('shivibes.otp.expires_minutes', 10) }} minutes.</p>
        </div>

        <div>
            <x-primary-button class="w-full justify-center">
                Verify email
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-100 pt-6 sm:flex-row">
        <form method="POST" action="{{ route('verification.otp.resend') }}">
            @csrf
            <button type="submit" class="text-sm font-medium text-brand-600 hover:text-brand-800 underline">
                Resend code
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-800 underline">
                Log out
            </button>
        </form>
    </div>
</x-guest-layout>
