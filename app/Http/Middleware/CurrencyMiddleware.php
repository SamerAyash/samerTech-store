<?php

namespace App\Http\Middleware;

use App\Services\CurrencyService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class CurrencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $currency = app(CurrencyService::class)->getCurrency($request);
        $request->merge(['currency' => $currency]);

        $lastUpdated = session('currency_last_update');
        $rates = session('currency_rates');

        if (!$rates || !$lastUpdated || now()->diffInMinutes($lastUpdated) >= 60) {
            try {
                $response = Http::get("https://open.er-api.com/v6/latest/QAR");

                if ($response->successful() && isset($response['rates'])) {
                    session([
                        'currency_rates' => $response['rates'],
                        'currency_last_update' => now(),
                    ]);
                    $rates = $response['rates'];
                }
            } catch (\Exception $e) {
                if (!$rates) {
                    session([
                        'currency_rates' => ['QAR' => 1],
                        'currency_last_update' => now(),
                    ]);
                }
            }
        }

        return $next($request);
    }
}
