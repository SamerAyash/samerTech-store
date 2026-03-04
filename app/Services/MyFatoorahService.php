<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Address;
use MyFatoorah\Library\API\Payment\MyFatoorahPayment;
use MyFatoorah\Library\MyFatoorah;
use Exception;
use Illuminate\Support\Facades\Log;

/**
 * MyFatoorah Payment Service
 * 
 * Handles all MyFatoorah payment gateway operations including:
 * - Creating payment invoices
 * - Processing payment callbacks
 * - Verifying payment status
 * - Handling webhooks
 */
class MyFatoorahService
{
    protected array $config;
    protected MyFatoorahPayment $paymentClient;

    public function __construct()
    {
        $this->config = [
            'apiKey'      => config('myfatoorah.api_key'),
            'isTest'      => config('myfatoorah.test_mode', true),
            'countryCode' => config('myfatoorah.country_iso', 'QAT'),
        ];

        $this->paymentClient = new MyFatoorahPayment($this->config);
    }

    /**
     * Create payment invoice and get redirect URL
     *
     * @param Order $order
     * @param int|null $paymentMethodId Payment method ID (0 for MyFatoorah invoice page)
     * @return array
     * @throws Exception
     */
    public function createPaymentInvoice(Order $order, ?int $paymentMethodId = 0): array
    {
        try {
            $payload = $this->buildPaymentPayload($order);
            
            $sessionId = null; // Can be used for tokenized payments
            $result = $this->paymentClient->getInvoiceURL(
                $payload,
                $paymentMethodId,
                $order->id,
                $sessionId
            );

            // Update order with MyFatoorah invoice ID
            $order->update([
                'myfatoorah_invoice_id' => $result['invoiceId'] ?? null,
                'payment_url' => $result['invoiceURL'] ?? null,
            ]);

            return [
                'success' => true,
                'invoice_id' => $result['invoiceId'] ?? null,
                'invoice_url' => $result['invoiceURL'] ?? null,
                'redirect_url' => $result['invoiceURL'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('MyFatoorah Payment Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            throw new Exception('Failed to create payment invoice: ' . $e->getMessage());
        }
    }

    /**
     * Build payment payload for MyFatoorah
     *
     * @param Order $order
     * @return array
     */
    protected function buildPaymentPayload(Order $order): array
    {
        $shippingAddress = $order->shippingAddress;
        $user = $order->user;
        
        // Get customer name
        $customerName = $shippingAddress 
            ? trim(($shippingAddress->first_name ?? '') . ' ' . ($shippingAddress->last_name ?? ''))
            : ($user->name ?? 'Customer');
        
        if (empty($customerName)) {
            $customerName = 'Customer';
        }

        // Get customer email
        $customerEmail = $user->email ?? 'customer@example.com';

        // Get customer mobile
        $customerMobile = $shippingAddress->phone ?? $user->phone ?? '';
        $mobileCountryCode = $this->extractCountryCode($customerMobile);

        // Build callback URLs
        $callbackUrl = route('myfatoorah.callback');
        $errorUrl = route('myfatoorah.callback');

        return [
            'CustomerName'       => $customerName,
            'InvoiceValue'       => (float) $order->total,
            'DisplayCurrencyIso' => $order->currency ?? 'QAR',
            'CustomerEmail'      => $customerEmail,
            'CallBackUrl'        => $callbackUrl,
            'ErrorUrl'           => $errorUrl,
            'MobileCountryCode'  => $mobileCountryCode,
            'CustomerMobile'     => $this->cleanPhoneNumber($customerMobile),
            'Language'           => app()->getLocale() === 'ar' ? 'ar' : 'en',
            'CustomerReference'  => $order->order_number,
            'SourceInfo'         => 'Laravel ' . app()->version() . ' - MyFatoorah Integration',
            'InvoiceItems'       => $this->buildInvoiceItems($order),
        ];
    }

    /**
     * Build invoice items array
     *
     * @param Order $order
     * @return array
     */
    protected function buildInvoiceItems(Order $order): array
    {
        $items = [];
        
        foreach ($order->orderItems as $orderItem) {
            $items[] = [
                'ItemName'  => $orderItem->product_name,
                'Quantity'   => $orderItem->quantity,
                'UnitPrice' => (float) $orderItem->price,
            ];
        }

        // Add shipping cost as an item if exists
        if ($order->shipping_cost > 0) {
            $items[] = [
                'ItemName'  => 'Shipping Cost',
                'Quantity'   => 1,
                'UnitPrice' => (float) $order->shipping_cost,
            ];
        }

        // Add discount as a negative item if exists
        // This ensures InvoiceValue matches the sum of all items
        if ($order->discount_amount > 0) {
            $discountName = $order->discountCode 
                ? ($order->discountCode->name ?? 'Discount')
                : 'Discount';
            
            $items[] = [
                'ItemName'  => $discountName,
                'Quantity'   => 1,
                'UnitPrice' => -(float) $order->discount_amount, // Negative value for discount
            ];
        }

        return $items;
    }

    /**
     * Get payment status from MyFatoorah
     *
     * @param string $paymentId Payment ID or Invoice ID
     * @param string $idType 'PaymentId' or 'InvoiceId'
     * @return object
     * @throws Exception
     */
    public function getPaymentStatus(string $paymentId, string $idType = 'PaymentId'): object
    {
        try {
            $paymentStatus = new \MyFatoorah\Library\API\Payment\MyFatoorahPaymentStatus($this->config);
            return $paymentStatus->getPaymentStatus($paymentId, $idType);
        } catch (Exception $e) {
            Log::error('MyFatoorah Get Payment Status Error: ' . $e->getMessage(), [
                'payment_id' => $paymentId,
                'id_type' => $idType,
            ]);

            throw new Exception('Failed to get payment status: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature
     *
     * @param array $data
     * @param string $signature
     * @param int $eventType
     * @return bool
     */
    public function verifyWebhookSignature(array $data, string $signature, int $eventType): bool
    {
        $secretKey = config('myfatoorah.webhook_secret_key');
        
        if (empty($secretKey)) {
            return false;
        }

        return MyFatoorah::isSignatureValid($data, $secretKey, $signature, $eventType);
    }

    /**
     * Extract country code from phone number
     *
     * @param string $phone
     * @return string
     */
    protected function extractCountryCode(string $phone): string
    {
        // Default to Qatar country code
        $defaultCode = '+974';
        
        if (empty($phone)) {
            return $defaultCode;
        }

        // Remove all non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Check if phone starts with country code
        if (preg_match('/^\+(\d{1,4})/', $phone, $matches)) {
            return '+' . $matches[1];
        }

        // Common country codes for Middle East
        $countryCodes = [
            'QAT' => '+974',
            'KWT' => '+965',
            'SAU' => '+966',
            'ARE' => '+971',
            'BHR' => '+973',
            'OMN' => '+968',
            'JOD' => '+962',
            'EGY' => '+20',
        ];

        $countryIso = $this->config['countryCode'] ?? 'QAT';
        return $countryCodes[$countryIso] ?? $defaultCode;
    }

    /**
     * Clean phone number (remove country code and non-numeric characters)
     *
     * @param string $phone
     * @return string
     */
    protected function cleanPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Remove country code if present
        $countryCode = $this->extractCountryCode($phone);
        $countryCodeDigits = preg_replace('/[^0-9]/', '', $countryCode);
        
        if (!empty($countryCodeDigits) && str_starts_with($phone, $countryCodeDigits)) {
            $phone = substr($phone, strlen($countryCodeDigits));
        }

        return $phone;
    }
}

