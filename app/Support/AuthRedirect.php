<?php

namespace App\Support;

use App\Models\User;
use App\Services\OtpVerificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class AuthRedirect
{
    /**
     * @param  array<string, scalar|null>  $query
     */
    public static function afterAuthentication(User $user, array $query = []): RedirectResponse
    {
        $otpService = app(OtpVerificationService::class);

        if ($otpService->needsVerification($user)) {
            try {
                $otpService->sendOtp($user);
            } catch (ValidationException) {
                // Cooldown or rate limit — user may use an existing code.
            } catch (\Throwable) {
                // Mail failures must not block login/registration redirect.
            }

            return redirect()->route('verification.otp');
        }

        $intended = session()->pull('url.intended');

        if ($intended && self::userCanAccessUrl($user, $intended)) {
            return redirect($intended);
        }

        $url = $user->defaultRedirectUrl();

        if ($query !== []) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator.http_build_query($query);
        }

        return redirect($url);
    }

    public static function userCanAccessUrl(User $user, string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';

        if (str_starts_with($path, '/admin')) {
            return $user->isAdmin();
        }

        return true;
    }
}
