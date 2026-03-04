<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * Currency Service
 * 
 * Handles all currency conversion business logic including:
 * - Converting prices from QAR to selected currency
 * - Converting prices from selected currency back to QAR
 * - Getting current currency from request
 * - Formatting prices with currency symbol
 */
class CurrencyService
{
    /**
     * Default base currency
     */
    const BASE_CURRENCY = 'QAR';

    /**
     * Get currency rates from session
     *
     * @return array
     */
    protected function getRates(): array
    {
        return Session::get('currency_rates', []);
    }

    /**
     * Get current currency from request
     *
     * @param Request|null $request
     * @return string
     */
    public function getCurrency(?Request $request = null): string
    {
        $request = $request ?? request();
        $currency = $request->header('X-Currency', self::BASE_CURRENCY);
        
        // Validate currency
        $allowedCurrencies = ['SAR','AED','KWD','QAR','OMR','BHD','GBP','USD','EUR'];
        if (!in_array($currency, $allowedCurrencies)) {
            return self::BASE_CURRENCY;
        }

        return $currency;
    }

    /**
     * Convert amount from QAR to selected currency
     *
     * @param float $amountQAR
     * @param string|null $currency
     * @param Request|null $request
     * @return float
     */
    public function convertFromQAR(float $amountQAR, ?string $currency = null, ?Request $request = null): float
    {
        $rates = $this->getRates();
        $currency = $currency ?? $this->getCurrency($request);

        // If currency is QAR or rate not available, return original amount
        if ($currency === self::BASE_CURRENCY || !isset($rates[$currency])) {
            return round($amountQAR, 2);
        }

        return round($amountQAR * $rates[$currency], 2);
    }

    /**
     * Convert amount from selected currency back to QAR
     *
     * @param float $amount
     * @param string|null $currency
     * @param Request|null $request
     * @return float
     */
    public function convertToQAR(float $amount, ?string $currency = null, ?Request $request = null): float
    {
        $rates = $this->getRates();
        $currency = $currency ?? $this->getCurrency($request);

        // If currency is QAR or rate not available, return original amount
        if ($currency === self::BASE_CURRENCY || !isset($rates[$currency]) || $rates[$currency] == 0) {
            return round($amount, 2);
        }

        // Convert back to QAR: amount / rate
        return round($amount / $rates[$currency], 2);
    }

    /**
     * Convert amount from QAR to selected currency with currency symbol
     *
     * @param float $amountQAR
     * @param string|null $currency
     * @param Request|null $request
     * @return string
     */
    public function convertWithSymbol(float $amountQAR, ?string $currency = null, ?Request $request = null): string
    {
        $currency = $currency ?? $this->getCurrency($request);
        $convertedAmount = $this->convertFromQAR($amountQAR, $currency, $request);

        return $convertedAmount . " " . $currency;
    }

    /**
     * Get currency symbol or code
     *
     * @param Request|null $request
     * @return string
     */
    public function getCurrencyCode(?Request $request = null): string
    {
        return $this->getCurrency($request);
    }

    /**
     * Get exchange rate for currency (1 QAR = rate × amount in that currency).
     * Used to persist order-time rate for display/audit.
     *
     * @param string $currency
     * @param Request|null $request
     * @return float|null
     */
    public function getRateForCurrency(string $currency, ?Request $request = null): ?float
    {
        if ($currency === self::BASE_CURRENCY) {
            return null;
        }
        $rates = $this->getRates();
        if (!isset($rates[$currency]) || (float) $rates[$currency] <= 0) {
            return null;
        }
        return (float) $rates[$currency];
    }

    /**
     * Get exchange rate for currency, fetching from API if not in session.
     * Used when creating orders from admin (session may not have rates).
     *
     * @param string $currency
     * @return float|null
     */
    public function getRateForCurrencyOrFetch(string $currency): ?float
    {
        if ($currency === self::BASE_CURRENCY) {
            return null;
        }
        $rate = $this->getRateForCurrency($currency, null);
        if ($rate !== null) {
            return $rate;
        }
        try {
            $response = Http::get('https://open.er-api.com/v6/latest/QAR');
            if ($response->successful() && isset($response['rates'][$currency])) {
                $rates = $response['rates'];
                $rate = (float) $rates[$currency];
                if ($rate > 0) {
                    Session::put('currency_rates', $rates);
                    Session::put('currency_last_update', now());
                    return $rate;
                }
            }
        } catch (\Exception $e) {
            // Return null on failure; order will still save without rate
        }
        return null;
    }
}
