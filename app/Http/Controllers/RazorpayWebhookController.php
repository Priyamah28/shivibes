<?php

namespace App\Http\Controllers;

use App\Services\Payment\OrderPaymentService;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class RazorpayWebhookController extends Controller
{
    public function __construct(
        private readonly RazorpayService $razorpayService,
        private readonly OrderPaymentService $orderPaymentService,
    ) {}

    public function __invoke(Request $request): Response
    {
        $payload = $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (! $this->razorpayService->verifyWebhookSignature($payload, $signature)) {
            return response('Invalid signature', 400);
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            Log::warning('Razorpay webhook invalid JSON', ['message' => $e->getMessage()]);

            return response('Invalid payload', 400);
        }

        $eventId = (string) ($data['id'] ?? $data['event_id'] ?? '');
        $eventType = (string) ($data['event'] ?? '');

        if ($eventId === '' || $eventType === '') {
            return response('Missing event metadata', 400);
        }

        try {
            $this->orderPaymentService->processWebhookEvent($eventId, $eventType, $data);
        } catch (Throwable $e) {
            Log::error('Razorpay webhook handler error', [
                'event_id' => $eventId,
                'event' => $eventType,
                'message' => $e->getMessage(),
            ]);

            return response('Processing failed', 500);
        }

        return response('OK', 200);
    }
}
