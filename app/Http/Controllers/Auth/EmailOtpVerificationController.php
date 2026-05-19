<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use App\Services\MailService;
use App\Services\OtpVerificationService;
use App\Services\PendingOtpSessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailOtpVerificationController extends Controller
{
    public function __construct(
        private readonly OtpVerificationService $otpVerificationService,
        private readonly PendingOtpSessionService $pendingOtpSession,
        private readonly AuthService $authService,
        private readonly MailService $mailService,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $this->pendingOtpSession->resolveUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->otpVerificationService->needsVerification($user)) {
            return $this->authService->completeLoginAfterOtp($request, $user);
        }

        return view('auth.verify-otp', [
            'email' => $user->email,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $user = $this->pendingOtpSession->resolveUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate([
            'otp' => ['required', 'string', 'digits:'.config('shivibes.otp.length', 6)],
        ]);

        $wasUnverified = $this->otpVerificationService->needsVerification($user);

        $this->otpVerificationService->verify($user, $request->string('otp')->toString());

        if ($wasUnverified) {
            $this->mailService->sendWelcomeMail($user);
        }

        return $this->authService->completeLoginAfterOtp($request, $user->fresh());
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $this->pendingOtpSession->resolveUser($request);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $this->otpVerificationService->needsVerification($user)) {
            return $this->authService->completeLoginAfterOtp($request, $user);
        }

        $this->authService->sendOtpWithLogging($user, 'resend');

        return back()->with('status', 'otp-sent');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $this->pendingOtpSession->resolveUser($request);

        if ($user) {
            $this->otpVerificationService->clearUserOtps($user);
        }

        $this->pendingOtpSession->clear($request);

        return redirect()
            ->route('login')
            ->with('status', 'verification-cancelled');
    }
}
