<?php

namespace App\Http\Middleware;

use App\Services\PendingOtpSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * OTP pages are guest-only with a valid pending OTP session.
 */
class RequirePendingOtpSession
{
    public function __construct(
        private readonly PendingOtpSessionService $pendingOtpSession,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        // Never allow an authenticated session on OTP pages (prevents refresh/tab bypass).
        if (Auth::check()) {
            Auth::logout();
        }

        if (! $this->pendingOtpSession->hasValidPending($request)) {
            $this->pendingOtpSession->clear($request);

            return redirect()
                ->route('login')
                ->withErrors(['email' => 'Your verification session expired. Please sign in again.']);
        }

        return $next($request);
    }
}
