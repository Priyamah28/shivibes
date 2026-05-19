<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class AuthRedirect
{
    /**
     * Redirect after the user is fully authenticated (OTP already verified when required).
     *
     * @param  array<string, scalar|null>  $query
     */
    public static function afterAuthentication(User $user, array $query = []): RedirectResponse
    {
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
