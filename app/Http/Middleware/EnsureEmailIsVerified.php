<?php

namespace App\Http\Middleware;

use App\Services\OtpVerificationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    public function __construct(
        private readonly OtpVerificationService $otpVerificationService,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $this->otpVerificationService->needsVerification($user)) {
            if ($request->routeIs('verification.otp', 'verification.otp.verify', 'verification.otp.resend', 'logout')) {
                return $next($request);
            }

            return redirect()->route('verification.otp');
        }

        return $next($request);
    }
}
