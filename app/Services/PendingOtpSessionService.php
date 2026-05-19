<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Holds pre-authentication OTP state in the session.
 * User must NOT be logged in until OTP is verified.
 */
class PendingOtpSessionService
{
    public const SESSION_USER_ID = 'otp_user_id';

    public const SESSION_REMEMBER = 'otp_remember';

    public const SESSION_EXPIRES_AT = 'otp_expires_at';

    public const SESSION_PURPOSE = 'otp_purpose';

    public const PURPOSE_LOGIN = 'login';

    public const PURPOSE_REGISTER = 'register';

    public const PURPOSE_EMAIL_CHANGE = 'email_change';

    /** Session lifetime for the OTP challenge (minutes). */
    public const SESSION_TTL_MINUTES = 30;

    public function establish(Request $request, User $user, bool $remember = false, string $purpose = self::PURPOSE_LOGIN): void
    {
        if (Auth::check()) {
            Auth::logout();
        }

        $request->session()->put([
            self::SESSION_USER_ID => $user->id,
            self::SESSION_REMEMBER => $remember,
            self::SESSION_EXPIRES_AT => now()->addMinutes(self::SESSION_TTL_MINUTES)->timestamp,
            self::SESSION_PURPOSE => $purpose,
        ]);

        $request->session()->save();
    }

    public function clear(Request $request): void
    {
        $request->session()->forget([
            self::SESSION_USER_ID,
            self::SESSION_REMEMBER,
            self::SESSION_EXPIRES_AT,
            self::SESSION_PURPOSE,
        ]);
    }

    public function hasValidPending(Request $request): bool
    {
        if (! $request->session()->has(self::SESSION_USER_ID)) {
            return false;
        }

        $expiresAt = $request->session()->get(self::SESSION_EXPIRES_AT);

        if (! $expiresAt || now()->timestamp > (int) $expiresAt) {
            return false;
        }

        return true;
    }

    public function resolveUser(Request $request): ?User
    {
        if (! $this->hasValidPending($request)) {
            return null;
        }

        $userId = $request->session()->get(self::SESSION_USER_ID);

        return User::query()->find($userId);
    }

    public function remember(Request $request): bool
    {
        return (bool) $request->session()->get(self::SESSION_REMEMBER, false);
    }

    public function purpose(Request $request): ?string
    {
        return $request->session()->get(self::SESSION_PURPOSE);
    }
}
