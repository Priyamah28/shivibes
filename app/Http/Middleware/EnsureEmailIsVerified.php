<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use App\Services\OtpVerificationService;
use App\Services\PendingOtpSessionService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defense in depth: unverified customers must never use authenticated routes.
 */
class EnsureEmailIsVerified
{
    public function __construct(
        private readonly OtpVerificationService $otpVerificationService,
        private readonly PendingOtpSessionService $pendingOtpSession,
        private readonly AuthService $authService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->isCustomer() && $this->otpVerificationService->needsVerification($user)) {
            Auth::logout();

            $this->pendingOtpSession->establish(
                $request,
                $user,
                false,
                PendingOtpSessionService::PURPOSE_LOGIN
            );

            try {
                $this->authService->sendOtpWithLogging($user, 'middleware_reverify');
            } catch (\Throwable) {
                // Logged in AuthService; still redirect to OTP page.
            }

            return redirect()
                ->route('verification.otp')
                ->withErrors(['email' => 'Please verify your email to continue.']);
        }

        return $next($request);
    }
}
