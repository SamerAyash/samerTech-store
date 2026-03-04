<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendOrderConfirmationEmailJob;
use App\Models\Order;
use App\Services\MyFatoorahService;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * MyFatoorah Payment Controller
 *
 * Handles MyFatoorah payment gateway callbacks and webhooks:
 * - Payment success/failure callbacks
 * - Webhook notifications
 * - Payment status verification
 */
class MyFatoorahController extends Controller
{
    protected MyFatoorahService $myFatoorahService;
    protected OrderService $orderService;

    public function __construct(
        MyFatoorahService $myFatoorahService,
        OrderService $orderService
    ) {
        $this->myFatoorahService = $myFatoorahService;
        $this->orderService = $orderService;
    }

    /**
     * Handle payment callback (success/failure)
     *
     * GET /api/myfatoorah/callback?paymentId=xxx
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|JsonResponse
     */
    public function callback(Request $request)
    {
        try {
            $paymentId = $request->query('paymentId');

            if (!$paymentId) {
                Log::error('MyFatoorah callback missing paymentId', [
                    'query_params' => $request->query(),
                ]);

                return $this->handleCallbackError('Payment ID is missing');
            }

            // Get payment status from MyFatoorah
            $paymentStatus = $this->myFatoorahService->getPaymentStatus($paymentId, 'PaymentId');

            // Find order by invoice ID
            $order = Order::where('myfatoorah_invoice_id', $paymentStatus->InvoiceId ?? null)
                ->orWhere('payment_transaction_id', $paymentId)
                ->first();

            if (!$order) {
                Log::error('Order not found for MyFatoorah callback', [
                    'payment_id' => $paymentId,
                    'invoice_id' => $paymentStatus->InvoiceId ?? null,
                ]);

                return $this->handleCallbackError('Order not found');
            }

            // Update order with payment transaction ID
            if (!$order->payment_transaction_id) {
                $order->update(['payment_transaction_id' => $paymentId]);
            }
            // Check payment status
            $isPaid = ($paymentStatus->InvoiceStatus ?? '') === 'Paid';
            $isFailed = in_array($paymentStatus->InvoiceStatus ?? '', ['Failed', 'Canceled', 'Expired']);
            if ($isPaid) {
                // Complete order and deduct quantities
                $this->orderService->completeOrder($order, $paymentId);

                SendOrderConfirmationEmailJob::dispatch($order->id);
                // Redirect to success page
                $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000'));
                $successUrl = "{$frontendUrl}/checkout/success?order={$order->order_number}&token={$order->access_token}";

                return redirect($successUrl);
            }
            elseif ($isFailed) {
                // Cancel order
                $this->orderService->cancelOrder($order, 'Payment failed via callback');
                // Send notification to admins about payment failure
                try {
                    notifyAdmins(new \App\Notifications\PaymentFailedNotification(
                        $order,
                        $paymentStatus->InvoiceStatus ?? 'Unknown'
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send payment failure notification', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                // Redirect to failure page
                $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000'));
                $failureUrl = "{$frontendUrl}/checkout/failure?order={$order->order_number}&token={$order->access_token}";

                return redirect($failureUrl);
            }
            else {
                $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000'));
                $pendingUrl = "{$frontendUrl}/checkout/pending?order={$order->order_number}&token={$order->access_token}";

                return redirect($pendingUrl);
            }
        } catch (Exception $e) {
            Log::error('MyFatoorah callback error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query_params' => $request->query(),
            ]);

            // Send system error notification
            try {
                notifyAdmins(new \App\Notifications\SystemErrorNotification(
                    $e->getMessage(),
                    'MyFatoorah Callback',
                    ['query_params' => $request->query()]
                ));
            } catch (\Exception $notificationError) {
                Log::error('Failed to send system error notification', [
                    'error' => $notificationError->getMessage(),
                ]);
            }

            return $this->handleCallbackError('An error occurred while processing payment');
        }
    }

    /**
     * Handle webhook notification from MyFatoorah
     *
     * POST /api/myfatoorah/webhook
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $data = $request->all();
            // Verify webhook signature if provided
            $signature = $request->header('X-MyFatoorah-Signature');
            $eventType = $request->header('X-MyFatoorah-Event-Type', 1);

            if ($signature && !$this->myFatoorahService->verifyWebhookSignature($data, $signature, (int) $eventType)) {
                Log::warning('MyFatoorah webhook signature verification failed', [
                    'signature' => $signature,
                    'event_type' => $eventType,
                ]);

                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Extract payment information
            $paymentId = $data['PaymentId'] ?? $data['paymentId'] ?? null;
            $invoiceId = $data['InvoiceId'] ?? $data['invoiceId'] ?? null;
            $invoiceStatus = $data['InvoiceStatus'] ?? $data['invoiceStatus'] ?? null;

            if (!$paymentId && !$invoiceId) {
                Log::error('MyFatoorah webhook missing payment/invoice ID', [
                    'data' => $data,
                ]);

                return response()->json(['error' => 'Missing payment/invoice ID'], 400);
            }

            // Find order
            $order = null;
            if ($invoiceId) {
                $order = Order::where('myfatoorah_invoice_id', $invoiceId)->first();
            }

            if (!$order && $paymentId) {
                $order = Order::where('payment_transaction_id', $paymentId)->first();
            }

            if (!$order) {
                Log::error('Order not found for MyFatoorah webhook', [
                    'payment_id' => $paymentId,
                    'invoice_id' => $invoiceId,
                ]);

                return response()->json(['error' => 'Order not found'], 404);
            }

            // Update order with payment transaction ID
            if ($paymentId && !$order->payment_transaction_id) {
                $order->update(['payment_transaction_id' => $paymentId]);
            }

            // Process payment status
            $isPaid = $invoiceStatus === 'Paid';
            $isFailed = in_array($invoiceStatus, ['Failed', 'Canceled', 'Expired']);

            if ($isPaid && $order->payment_status !== 'paid') {
                // Complete order and deduct quantities
                $this->orderService->completeOrder($order, $paymentId);

            } elseif ($isFailed && $order->payment_status !== 'failed') {
                // Cancel order
                $this->orderService->cancelOrder($order, 'Payment failed via webhook');
                // Send notification to admins about payment failure
                try {
                    notifyAdmins(new \App\Notifications\PaymentFailedNotification(
                        $order,
                        $invoiceStatus
                    ));
                } catch (\Exception $e) {
                    Log::error('Failed to send payment failure notification', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return response()->json(['success' => true, 'message' => 'Webhook processed']);
        } catch (Exception $e) {
            Log::error('MyFatoorah webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $request->all(),
            ]);

            // Send system error notification
            try {
                notifyAdmins(new \App\Notifications\SystemErrorNotification(
                    $e->getMessage(),
                    'MyFatoorah Webhook',
                    ['data' => $request->all()]
                ));
            } catch (\Exception $notificationError) {
                Log::error('Failed to send system error notification', [
                    'error' => $notificationError->getMessage(),
                ]);
            }

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Verify payment status manually
     *
     * GET /api/myfatoorah/verify/{order}
     *
     * @param int|string $orderId Order ID or Order Number
     * @return JsonResponse
     */
    public function verifyPayment($orderId): JsonResponse
    {
        try {
            // Find order by ID or order number
            $user = request()->user();
            $guestId = request()->header('X-Guest-Id');
            $order = Order::where('id', $orderId)
                ->orWhere('order_number', $orderId)
                ->when($user, function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->when(!$user && $guestId, function ($query) use ($guestId) {
                    $query->where('guest_id', $guestId);
                })
                ->first();

            if(!$order || (!$user && !$guestId)){
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if (!$order->myfatoorah_invoice_id && !$order->payment_transaction_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order does not have payment information',
                ], 400);
            }

            $paymentId = $order->payment_transaction_id ?? $order->myfatoorah_invoice_id;
            $idType = $order->payment_transaction_id ? 'PaymentId' : 'InvoiceId';

            $paymentStatus = $this->myFatoorahService->getPaymentStatus($paymentId, $idType);

            $isPaid = ($paymentStatus->InvoiceStatus ?? '') === 'Paid';
            $isFailed = in_array($paymentStatus->InvoiceStatus ?? '', ['Failed', 'Canceled', 'Expired']);

            if ($isPaid && $order->payment_status !== 'paid') {
                $this->orderService->completeOrder($order, $paymentId);
            } elseif ($isFailed && $order->payment_status !== 'failed') {
                $this->orderService->cancelOrder($order, 'Payment verification failed');
            }

            return response()->json([
                'success' => true,
                'payment_status' => $paymentStatus->InvoiceStatus ?? 'Unknown',
                'order_status' => $order->fresh()->payment_status,
            ]);
        } catch (Exception $e) {
            Log::error('Payment verification error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment status',
            ], 500);
        }
    }

    /**
     * Handle callback error
     *
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function handleCallbackError(string $message)
    {
        $frontendUrl = config('app.frontend_url', env('FRONTEND_URL', 'http://localhost:3000'));
        $errorUrl = "{$frontendUrl}/checkout/error?message=" . urlencode($message);

        return redirect($errorUrl);
    }
}

