<?php

namespace Tests\Feature\Auth;

use App\Models\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_verification_screen_redirects_to_otp_page(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertRedirect(route('verification.otp'));
    }

    public function test_otp_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email/otp');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified_with_valid_otp(): void
    {
        $user = User::factory()->unverified()->create();
        $plainOtp = '123456';

        EmailVerificationOtp::create([
            'user_id' => $user->id,
            'otp_hash' => Hash::make($plainOtp),
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($user)->post('/verify-email/otp', [
            'otp' => $plainOtp,
        ]);

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('home'));
    }

    public function test_email_is_not_verified_with_invalid_otp(): void
    {
        $user = User::factory()->unverified()->create();

        EmailVerificationOtp::create([
            'user_id' => $user->id,
            'otp_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($user)->post('/verify-email/otp', [
            'otp' => '000000',
        ]);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
