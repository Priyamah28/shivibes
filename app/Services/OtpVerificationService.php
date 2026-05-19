<?php

namespace App\Services;

use App\Models\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpVerificationService
{
    public function __construct(
        private readonly MailService $mailService,
    ) {}

    public function needsVerification(User $user): bool
    {
        return $user->isCustomer() && $user->email_verified_at === null;
    }

    /**
     * @return array{sent: bool, expires_at: \Illuminate\Support\Carbon}
     */
    public function sendOtp(User $user): array
    {
        $this->ensureCanResend($user);

        EmailVerificationOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('verified_at')
            ->delete();

        $plainOtp = $this->generatePlainOtp();
        $expiresMinutes = (int) config('shivibes.otp.expires_minutes', 10);

        $record = EmailVerificationOtp::create([
            'user_id' => $user->id,
            'otp_hash' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes($expiresMinutes),
        ]);

        $this->mailService->sendOtpMail($user, $plainOtp, $expiresMinutes);

        RateLimiter::hit($this->resendThrottleKey($user), config('shivibes.otp.resend_cooldown_seconds', 60));
        RateLimiter::hit($this->hourlyResendKey($user), 3600);

        return [
            'sent' => true,
            'expires_at' => $record->expires_at,
        ];
    }

    public function verify(User $user, string $otp): bool
    {
        $record = EmailVerificationOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $record || $record->isExpired()) {
            throw ValidationException::withMessages([
                'otp' => 'This code has expired. Please request a new one.',
            ]);
        }

        $maxAttempts = (int) config('shivibes.otp.max_verify_attempts', 5);

        if ($record->attempts >= $maxAttempts) {
            throw ValidationException::withMessages([
                'otp' => 'Too many incorrect attempts. Please request a new code.',
            ]);
        }

        if (! Hash::check($otp, $record->otp_hash)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => 'The verification code is incorrect.',
            ]);
        }

        $record->update(['verified_at' => now()]);

        $user->forceFill(['email_verified_at' => now()])->save();

        EmailVerificationOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('verified_at')
            ->delete();

        return true;
    }

    public function ensureCanResend(User $user): void
    {
        $cooldown = (int) config('shivibes.otp.resend_cooldown_seconds', 60);

        if (RateLimiter::tooManyAttempts($this->resendThrottleKey($user), 1)) {
            $seconds = RateLimiter::availableIn($this->resendThrottleKey($user));

            throw ValidationException::withMessages([
                'otp' => "Please wait {$seconds} seconds before requesting another code.",
            ]);
        }

        $maxPerHour = (int) config('shivibes.otp.max_resends_per_hour', 5);

        if (RateLimiter::tooManyAttempts($this->hourlyResendKey($user), $maxPerHour)) {
            throw ValidationException::withMessages([
                'otp' => 'You have requested too many codes. Please try again later.',
            ]);
        }
    }

    private function generatePlainOtp(): string
    {
        $length = (int) config('shivibes.otp.length', 6);
        $max = (10 ** $length) - 1;
        $number = random_int(0, $max);

        return str_pad((string) $number, $length, '0', STR_PAD_LEFT);
    }

    private function resendThrottleKey(User $user): string
    {
        return 'otp-resend:'.$user->id;
    }

    private function hourlyResendKey(User $user): string
    {
        return 'otp-resend-hour:'.$user->id;
    }
}
