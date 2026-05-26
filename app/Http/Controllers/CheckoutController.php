<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\VerifyRazorpayPaymentRequest;
use App\Models\Order;
use App\Services\CheckoutService;
use App\Services\MailService;
use App\Services\Payment\OrderPaymentService;
use App\Services\Payment\RazorpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
        private readonly RazorpayService $razorpayService,
        private readonly OrderPaymentService $orderPaymentService,
        private readonly MailService $mailService,
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $user = $request->user();
        $subtotal = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

        return view('store.checkout', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'addresses' => $user->addresses()->orderByDesc('is_default')->get(),
            'defaultAddress' => $user->defaultAddress(),
            'razorpayKey' => $this->razorpayService->publicKey(),
            'razorpayConfigured' => $this->razorpayService->isConfigured(),
            'codEnabled' => (bool) config('razorpay.cod_enabled'),
        ]);
    }

    public function store(CheckoutRequest $request): JsonResponse|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Your cart is empty.'], 422);
            }

            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validated();
        $user = $request->user();

        if ($validated['payment_method'] === CheckoutService::PAYMENT_COD) {
            return $this->handleCodCheckout($request, $user, $validated, $cart);
        }

        return $this->handleRazorpayCheckout($request, $user, $validated, $cart);
    }

    public function verify(VerifyRazorpayPaymentRequest $request): JsonResponse
    {
        $order = Order::query()->findOrFail($request->integer('order_id'));

        Gate::authorize('view', $order);

        if ($order->payment_status === Order::PAYMENT_PAID) {
            return response()->json([
                'success' => true,
                'message' => 'Payment already confirmed.',
                'redirect' => route('account.orders.show', $order),
            ]);
        }

        try {
            $result = $this->orderPaymentService->markPaid(
                $order,
                $request->string('razorpay_order_id')->toString(),
                $request->string('razorpay_payment_id')->toString(),
                $request->string('razorpay_signature')->toString(),
                ['verified_via' => 'checkout_ajax'],
                $request->user(),
                $request,
            );

            $order = $result['order'];

            return response()->json([
                'success' => true,
                'message' => $result['already_paid']
                    ? 'Payment already confirmed.'
                    : 'Payment successful! Thank you for your order.',
                'redirect' => route('account.orders.show', $order),
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Insufficient stock for this order.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Throwable $e) {
            Log::error('Razorpay payment verification failed', [
                'order_id' => $order->id,
                'user_id' => $request->user()?->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed. If amount was deducted, contact support with your order number.',
            ], 422);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, array<string, mixed>>  $cart
     */
    private function handleRazorpayCheckout(Request $request, $user, array $validated, array $cart): JsonResponse
    {
        if (! $this->razorpayService->isConfigured()) {
            return response()->json([
                'message' => 'Online payment is not configured. Please contact support.',
            ], 503);
        }

        try {
            $order = $this->checkoutService->createPendingOrder($user, $validated, $cart);
            $razorpayOrder = $this->razorpayService->createOrder($order);

            $this->checkoutService->attachRazorpayOrder($order, $razorpayOrder['id'], [
                'razorpay_amount_paise' => $razorpayOrder['amount'],
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'amount' => $razorpayOrder['amount'],
                'currency' => $razorpayOrder['currency'],
                'razorpay_order_id' => $razorpayOrder['id'],
                'razorpay_key' => $this->razorpayService->publicKey(),
                'prefill' => [
                    'name' => $order->shippingSnapshot()['full_name'] ?? $user->name,
                    'email' => $user->email,
                    'contact' => $order->shippingSnapshot()['phone'] ?? null,
                ],
                'notes' => [
                    'order_id' => (string) $order->id,
                    'order_number' => $order->order_number,
                ],
            ]);
        } catch (Throwable $e) {
            Log::error('Razorpay checkout initiation failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Unable to start payment. Please try again.',
            ], 500);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     * @param  array<string, array<string, mixed>>  $cart
     */
    private function handleCodCheckout(Request $request, $user, array $validated, array $cart): RedirectResponse|JsonResponse
    {
        try {
            $order = $this->checkoutService->placeCodOrder($user, $validated, $cart, $request);
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => collect($e->errors())->flatten()->first() ?? 'Please check your cart.',
                    'errors' => $e->errors(),
                ], 422);
            }

            return redirect()->route('checkout.index')->withErrors($e->errors());
        }

        $order->load(['items.product', 'user', 'customer']);
        $this->mailService->sendOrderPlacedMail($order);

        $redirect = route('account.orders.show', $order);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order placed! Pay on delivery.',
                'redirect' => $redirect,
            ]);
        }

        return redirect($redirect)->with('success', 'Order placed! Pay cash on delivery when your order arrives.');
    }
}
