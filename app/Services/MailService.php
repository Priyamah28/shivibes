<?php

namespace App\Services;

use App\Mail\AdminNotificationMail;
use App\Mail\CorporateInquiryAdminMail;
use App\Mail\CorporateInquiryCustomerMail;
use App\Mail\OtpMail;
use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusUpdatedMail;
use App\Mail\WelcomeMail;
use App\Models\CorporateInquiry;
use App\Models\Order;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    public function sendOtpMail(User $user, string $otp, int $expiresMinutes): bool
    {
        // OTP is time-sensitive: always send synchronously (same path as password reset).
        return $this->send(
            new OtpMail($user, $otp, $expiresMinutes),
            $user->email,
            'otp',
            ['user_id' => $user->id],
            forceSync: true,
        );
    }

    public function sendWelcomeMail(User $user): bool
    {
        return $this->send(new WelcomeMail($user), $user->email, 'welcome', [
            'user_id' => $user->id,
        ]);
    }

    public function sendOrderPlacedMail(Order $order): bool
    {
        $email = $order->user?->email ?? $order->customer?->email;

        if (! $email) {
            return false;
        }

        return $this->send(new OrderPlacedMail($order), $email, 'order_placed', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
        ]);
    }

    public function sendOrderStatusUpdatedMail(Order $order, string $previousStatus): bool
    {
        $email = $order->user?->email ?? $order->customer?->email;

        if (! $email) {
            return false;
        }

        return $this->send(
            new OrderStatusUpdatedMail($order, $previousStatus),
            $email,
            'order_status_updated',
            [
                'order_id' => $order->id,
                'status' => $order->status,
            ]
        );
    }

    public function sendCorporateInquiryMail(CorporateInquiry $inquiry): bool
    {
        $adminSent = false;
        $context = ['inquiry_id' => $inquiry->id];

        foreach (config('shivibes.mail.admin_emails', []) as $email) {
            if ($this->send(new CorporateInquiryAdminMail($inquiry), $email, 'corporate_inquiry_admin', $context)) {
                $adminSent = true;
            }
        }

        $customerSent = $this->send(
            new CorporateInquiryCustomerMail($inquiry),
            $inquiry->email,
            'corporate_inquiry_customer',
            ['inquiry_id' => $inquiry->id]
        );

        return $adminSent || $customerSent;
    }

    /**
     * @param  array<string, mixed>  $details
     */
    public function sendAdminNotification(
        string $title,
        string $message,
        array $details = [],
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        string $logContext = 'admin_notification',
    ): bool {
        $recipients = config('shivibes.mail.admin_emails', []);

        if ($recipients === []) {
            Log::warning('Mail skipped: no admin notification emails configured.', $details);

            return false;
        }

        $sent = false;

        foreach ($recipients as $email) {
            if ($this->send(
                new AdminNotificationMail($title, $message, $details, $actionUrl, $actionLabel),
                $email,
                $logContext,
                $details
            )) {
                $sent = true;
            }
        }

        return $sent;
    }

    /**
     * @param  string|array<int, string>  $to
     * @param  array<string, mixed>  $context
     */
    protected function send(
        Mailable $mailable,
        string|array $to,
        string $type,
        array $context = [],
        bool $forceSync = false,
    ): bool {
        $useQueue = ! $forceSync && (bool) config('shivibes.mail.use_queue');
        $mailDefault = (string) config('mail.default');

        try {
            $mailer = Mail::to($to);

            if ($useQueue) {
                $mailer->queue($mailable);
                Log::info('Mail queued.', array_merge($context, [
                    'type' => $type,
                    'to' => is_array($to) ? implode(',', $to) : $to,
                    'mailable' => $mailable::class,
                ]));
            } else {
                $mailer->send($mailable);
                Log::info('Mail sent synchronously.', array_merge($context, [
                    'type' => $type,
                    'to' => is_array($to) ? implode(',', $to) : $to,
                    'mailable' => $mailable::class,
                    'mailer' => $mailDefault,
                    'force_sync' => $forceSync,
                ]));
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Mail delivery failed.', array_merge($context, [
                'type' => $type,
                'to' => is_array($to) ? implode(',', $to) : $to,
                'mailable' => $mailable::class,
                'error' => $e->getMessage(),
            ]));

            return false;
        }
    }
}
