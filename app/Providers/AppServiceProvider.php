<?php

namespace App\Providers;

use App\Models\Setting;
use App\Services\CurrencyService;
use App\Services\GeoLocationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Force HTTPS URLs when the request is secure
        if (request()->isSecure()) {
            URL::forceScheme('https');
        }

        View::share('settings', []);

        try {
            if (Schema::hasTable('settings')) {
                $publicSettings = Setting::public()->ordered()->get()->mapWithKeys(function (Setting $setting) {
                    $value = $setting->formatted_value;

                    if ($setting->isFileType() && $value) {
                        $value = Storage::disk('public')->exists($value) ? Storage::disk('public')->url($value) : $value;
                    }

                    return [$setting->key => $value];
                })->toArray();

                View::share('settings', $publicSettings);
            }
        } catch (\Throwable $e) {
            report($e);
        }

        // Share currency information with all views
        try {
            $currencyService = app(CurrencyService::class);
            // Get currency from session or detect from IP
            $userCurrency = session('currency');
            
            if (!$userCurrency) {
                $geoService = app(GeoLocationService::class);
                $userCurrency = $geoService->getCurrencyFromIP(request()->ip());
                session(['currency' => $userCurrency]);
            }
            
            View::share('userCurrency', $userCurrency);
            View::share('currencySymbol', $currencyService->getCurrencySymbol($userCurrency));
        } catch (\Throwable $e) {
            report($e);
            View::share('userCurrency', 'OMR');
            View::share('currencySymbol', 'ر.ع.');
        }
    }
}
