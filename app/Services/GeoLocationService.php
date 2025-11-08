<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Cache duration in minutes
     */
    const CACHE_DURATION = 1440; // 24 hours

    /**
     * Map country codes to currency codes
     */
    private $countryToCurrency = [
        'OM' => 'OMR', // Oman
        'SA' => 'SAR', // Saudi Arabia
        'AE' => 'AED', // UAE
        'EG' => 'EGP', // Egypt
        'KW' => 'KWD', // Kuwait
        'QA' => 'QAR', // Qatar
        'BH' => 'BHD', // Bahrain
        'US' => 'USD', // United States
        'GB' => 'GBP', // United Kingdom
        'EU' => 'EUR', // European Union
    ];

    /**
     * Get country code from IP address
     */
    public function getCountryFromIP(string $ip): ?string
    {
        // Skip localhost IPs
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return 'OM'; // Default to Oman for local development
        }

        $cacheKey = "country_from_ip_{$ip}";

        return Cache::remember($cacheKey, now()->addMinutes(self::CACHE_DURATION), function () use ($ip) {
            try {
                // Using ip-api.com free tier (no API key required, 45 requests/minute)
                $response = Http::timeout(5)->get("http://ip-api.com/json/{$ip}?fields=status,countryCode");

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success' && isset($data['countryCode'])) {
                        $countryCode = $data['countryCode'];
                        
                        Log::info("Country detected from IP", [
                            'ip' => $ip,
                            'country_code' => $countryCode
                        ]);

                        return $countryCode;
                    }
                }

                // Fallback: Try alternative service
                return $this->getCountryFromIPFallback($ip);
            } catch (\Exception $e) {
                Log::error("Failed to detect country from IP", [
                    'error' => $e->getMessage(),
                    'ip' => $ip
                ]);

                return $this->getCountryFromIPFallback($ip);
            }
        });
    }

    /**
     * Fallback method for country detection
     */
    private function getCountryFromIPFallback(string $ip): ?string
    {
        try {
            // Alternative: Using ipapi.co (free tier: 1000 requests/day)
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/country_code/");

            if ($response->successful()) {
                $countryCode = trim($response->body());
                
                if (strlen($countryCode) === 2) {
                    return $countryCode;
                }
            }
        } catch (\Exception $e) {
            Log::error("Fallback country detection failed", [
                'error' => $e->getMessage()
            ]);
        }

        // Default to Oman if all methods fail
        return 'OM';
    }

    /**
     * Get currency code from country code
     */
    public function getCurrencyFromCountry(?string $countryCode): string
    {
        if (!$countryCode) {
            return 'OMR'; // Default to OMR
        }

        return $this->countryToCurrency[strtoupper($countryCode)] ?? 'OMR';
    }

    /**
     * Get currency from IP address
     */
    public function getCurrencyFromIP(string $ip): string
    {
        $countryCode = $this->getCountryFromIP($ip);
        return $this->getCurrencyFromCountry($countryCode);
    }
}

