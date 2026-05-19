<?php

namespace Tests\Feature\Auth;

use App\Models\EmailVerificationOtp;
use App\Models\User;
use App\Services\PendingOtpSessionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    private function pendingOtpSession(User $user, bool $remember = false): array
    {
        return [
            PendingOtpSessionService::SESSION_USER_ID => $user->id,
            PendingOtpSessionService::SESSION_REMEMBER => $remember,
            PendingOtpSessionService::SESSION_EXPIRES_AT => now()->addMinutes(30)->timestamp,
            PendingOtpSessionService::SESSION_PURPOSE => PendingOtpSessionService::PURPOSE_LOGIN,
        ];
    }

    public function test_unverified_login_does_not_authenticate_user(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('verification.otp'));
    }

    public function test_otp_screen_can_be_rendered_with_pending_session(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->withSession($this->pendingOtpSession($user))
            ->get('/verify-email/otp');

        $response->assertStatus(200);
        $this->assertGuest();
    }

    public function test_email_can_be_verified_with_valid_otp(): void
    {
        $user = User::factory()->unverified()->create();
        $plainOtp = '123456';

        EmailVerificationOtp::create([
            'user_id' => $user->id,
            'otp_hash' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(5),
        ]);

        $response = $this->withSession($this->pendingOtpSession($user))
            ->post('/verify-email/otp', [
                'otp' => $plainOtp,
            ]);

        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('home'));
    }

    public function test_email_is_not_verified_with_invalid_otp(): void
    {
        $user = User::factory()->unverified()->create();

        EmailVerificationOtp::create([
            'user_id' => $user->id,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->withSession($this->pendingOtpSession($user))
            ->post('/verify-email/otp', [
                'otp' => '000000',
            ]);

        $this->assertGuest();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_refreshing_otp_page_does_not_authenticate_user(): void
    {
        $user = User::factory()->unverified()->create();

        $this->withSession($this->pendingOtpSession($user))
            ->get('/verify-email/otp')
            ->assertStatus(200);

        $this->withSession($this->pendingOtpSession($user))
            ->get('/verify-email/otp')
            ->assertStatus(200);

        $this->assertGuest();
    }
}
