<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    /**
     * Base currency (USD)
     */
    const BASE_CURRENCY = 'USD';

    /**
     * Default currency for payment gateway (OMR)
     */
    const PAYMENT_CURRENCY = 'OMR';

    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 60; // 1 hour

    /**
     * Get exchange rate from USD to target currency
     * Uses free API: exchangerate-api.com (no API key required)
     */
    public function getExchangeRate(string $toCurrency): float
    {
        $cacheKey = "exchange_rate_usd_to_{$toCurrency}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_DURATION), function () use ($toCurrency) {
            try {
                // Using exchangerate-api.com free tier (no API key needed)
                $response = Http::timeout(5)->get("https://api.exchangerate-api.com/v4/latest/" . self::BASE_CURRENCY);

                if ($response->successful()) {
                    $data = $response->json();
                    $rate = $data['rates'][strtoupper($toCurrency)] ?? null;

                    if ($rate) {
                        Log::info("Exchange rate fetched", [
                            'from' => self::BASE_CURRENCY,
                            'to' => $toCurrency,
                            'rate' => $rate
                        ]);
                        return (float) $rate;
                    }
                }

                // Fallback: Use alternative free API
                return $this->getExchangeRateFallback($toCurrency);
            } catch (\Exception $e) {
                Log::error("Failed to fetch exchange rate", [
                    'error' => $e->getMessage(),
                    'to_currency' => $toCurrency
                ]);

                // Return fallback rate
                return $this->getExchangeRateFallback($toCurrency);
            }
        });
    }

    /**
     * Fallback method using alternative free API
     */
    private function getExchangeRateFallback(string $toCurrency): float
    {
        try {
            // Alternative: Using fixer.io free tier (requires API key but has fallback rates)
            // Or use hardcoded fallback rates for common currencies
            $fallbackRates = [
                'OMR' => 0.385,  // 1 USD = 0.385 OMR (approximate)
                'SAR' => 3.75,
                'AED' => 3.67,
                'EGP' => 30.90,
                'EUR' => 0.92,
                'KWD' => 0.307,
                'QAR' => 3.64,
                'BHD' => 0.377,
            ];

            $rate = $fallbackRates[strtoupper($toCurrency)] ?? 1.0;

            Log::warning("Using fallback exchange rate", [
                'to_currency' => $toCurrency,
                'rate' => $rate
            ]);

            return $rate;
        } catch (\Exception $e) {
            Log::error("Fallback exchange rate failed", [
                'error' => $e->getMessage()
            ]);

            return 1.0; // Return 1.0 as last resort
        }
    }

    /**
     * Convert amount from USD to target currency
     */
    public function convert(float $amount, string $toCurrency): float
    {
        if (strtoupper($toCurrency) === self::BASE_CURRENCY) {
            return $amount;
        }

        $rate = $this->getExchangeRate($toCurrency);
        return round($amount * $rate, 2);
    }

    /**
     * Convert amount from target currency to USD
     */
    public function convertToBase(float $amount, string $fromCurrency): float
    {
        if (strtoupper($fromCurrency) === self::BASE_CURRENCY) {
            return $amount;
        }

        $rate = $this->getExchangeRate($fromCurrency);
        return round($amount / $rate, 2);
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(string $currency): string
    {
        $symbols = [
            'USD' => '$',
            'OMR' => 'ر.ع.',
            'SAR' => 'ر.س',
            'AED' => 'د.إ',
            'EGP' => 'ج.م',
            'EUR' => '€',
            'KWD' => 'د.ك',
            'QAR' => 'ر.ق',
            'BHD' => 'د.ب',
        ];

        return $symbols[strtoupper($currency)] ?? strtoupper($currency);
    }

    /**
     * Get currency name in Arabic
     */
    public function getCurrencyName(string $currency): string
    {
        $names = [
            'USD' => 'دولار أمريكي',
            'OMR' => 'ريال عماني',
            'SAR' => 'ريال سعودي',
            'AED' => 'درهم إماراتي',
            'EGP' => 'جنيه مصري',
            'EUR' => 'يورو',
            'KWD' => 'دينار كويتي',
            'QAR' => 'ريال قطري',
            'BHD' => 'دينار بحريني',
        ];

        return $names[strtoupper($currency)] ?? strtoupper($currency);
    }

    /**
     * Format price with currency
     */
    public function formatPrice(float $amount, string $currency, bool $showSymbol = true): string
    {
        $formatted = number_format($amount, 2);
        
        if ($showSymbol) {
            $symbol = $this->getCurrencySymbol($currency);
            return $formatted . ' ' . $symbol;
        }

        return $formatted;
    }
}

