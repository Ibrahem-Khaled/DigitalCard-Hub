<?php

use App\Services\CurrencyService;
use App\Services\GeoLocationService;

if (!function_exists('getUserCurrency')) {
    /**
     * Get user's currency from session or IP address
     */
    function getUserCurrency(): string
    {
        // Check if currency is set in session
        if (session()->has('currency')) {
            return session('currency');
        }

        // Otherwise, detect from IP
        $geoService = app(GeoLocationService::class);
        $ip = request()->ip();
        $currency = $geoService->getCurrencyFromIP($ip);
        
        // Store in session for future use
        session(['currency' => $currency]);
        
        return $currency;
    }
}

if (!function_exists('convertPrice')) {
    /**
     * Convert price from USD to target currency
     */
    function convertPrice(float $price, ?string $currency = null): float
    {
        // Use currency from session if not specified
        $currency = $currency ?? getUserCurrency();
        $currencyService = app(CurrencyService::class);
        return $currencyService->convert($price, $currency);
    }
}

if (!function_exists('formatPrice')) {
    /**
     * Format price with currency symbol
     */
    function formatPrice(float $price, ?string $currency = null, bool $showSymbol = true): string
    {
        $currency = $currency ?? getUserCurrency();
        $convertedPrice = convertPrice($price, $currency);
        $currencyService = app(CurrencyService::class);
        return $currencyService->formatPrice($convertedPrice, $currency, $showSymbol);
    }
}

if (!function_exists('getCurrencySymbol')) {
    /**
     * Get currency symbol
     */
    function getCurrencySymbol(?string $currency = null): string
    {
        $currency = $currency ?? getUserCurrency();
        $currencyService = app(CurrencyService::class);
        return $currencyService->getCurrencySymbol($currency);
    }
}

if (!function_exists('convertToPaymentCurrency')) {
    /**
     * Convert price to payment gateway currency (OMR)
     */
    function convertToPaymentCurrency(float $price): float
    {
        $currencyService = app(CurrencyService::class);
        return $currencyService->convert($price, CurrencyService::PAYMENT_CURRENCY);
    }
}

