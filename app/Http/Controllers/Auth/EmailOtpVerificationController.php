<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\MailService;
use App\Services\OtpVerificationService;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailOtpVerificationController extends Controller
{
    public function __construct(
        private readonly OtpVerificationService $otpVerificationService,
        private readonly MailService $mailService,
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $this->otpVerificationService->needsVerification($user)) {
            return AuthRedirect::afterAuthentication($user);
        }

        return view('auth.verify-otp', [
            'email' => $user->email,
        ]);
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:'.config('shivibes.otp.length', 6)],
        ]);

        $user = $request->user();
        $wasUnverified = $this->otpVerificationService->needsVerification($user);

        $this->otpVerificationService->verify($user, $request->string('otp')->toString());

        if ($wasUnverified) {
            $this->mailService->sendWelcomeMail($user);
        }

        return AuthRedirect::afterAuthentication($user->fresh());
    }

    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $this->otpVerificationService->needsVerification($user)) {
            return redirect()->route('home');
        }

        $this->otpVerificationService->sendOtp($user);

        return back()->with('status', 'otp-sent');
    }
}
