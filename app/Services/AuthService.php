<?php

namespace App\Services;

use App\Models\User;
use App\Support\AuthRedirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Coordinates login, registration, and post-OTP authentication.
 */
class AuthService
{
    public function __construct(
        private readonly OtpVerificationService $otpVerificationService,
        private readonly PendingOtpSessionService $pendingOtpSession,
        private readonly MailService $mailService,
    ) {}

    /**
     * After valid credentials: either log in immediately or start OTP challenge (guest session).
     */
    public function handlePostCredentialLogin(Request $request, User $user, bool $remember): RedirectResponse
    {
        if ($user->isAdmin() || ! $this->otpVerificationService->needsVerification($user)) {
            Auth::login($user, $remember);
            $request->session()->regenerate();

            return AuthRedirect::afterAuthentication($user);
        }

        return $this->beginOtpChallenge($request, $user, $remember, PendingOtpSessionService::PURPOSE_LOGIN);
    }

    public function handlePostRegistration(Request $request, User $user): RedirectResponse
    {
        return $this->beginOtpChallenge($request, $user, false, PendingOtpSessionService::PURPOSE_REGISTER);
    }

    public function beginOtpChallenge(
        Request $request,
        User $user,
        bool $remember,
        string $purpose,
    ): RedirectResponse {
        $this->pendingOtpSession->establish($request, $user, $remember, $purpose);

        $this->sendOtpWithLogging($user, $purpose);

        return redirect()
            ->route('verification.otp')
            ->with('status', 'otp-sent');
    }

    /**
     * Complete login after successful OTP verification.
     */
    public function completeLoginAfterOtp(Request $request, User $user): RedirectResponse
    {
        $remember = $this->pendingOtpSession->remember($request);
        $purpose = $this->pendingOtpSession->purpose($request);

        $this->pendingOtpSession->clear($request);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        Log::info('User authenticated after OTP verification.', [
            'user_id' => $user->id,
            'purpose' => $purpose,
        ]);

        return AuthRedirect::afterAuthentication($user);
    }

    public function sendOtpWithLogging(User $user, string $context = 'otp'): void
    {
        try {
            $result = $this->otpVerificationService->sendOtp($user);

            Log::info('OTP generated and mail dispatch attempted.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'context' => $context,
                'expires_at' => $result['expires_at']->toIso8601String(),
                'mail_sent' => $result['mail_sent'],
            ]);

            if (! $result['mail_sent']) {
                Log::warning('OTP mail was not sent successfully.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'context' => $context,
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::notice('OTP resend throttled.', [
                'user_id' => $user->id,
                'context' => $context,
                'message' => $e->getMessage(),
            ]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('OTP send failed with exception.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'context' => $context,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
