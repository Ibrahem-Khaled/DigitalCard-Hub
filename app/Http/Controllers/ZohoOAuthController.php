<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;

class ZohoOAuthController extends Controller
{
    /**
     * Show Zoho OAuth setup page
     */
    public function setup()
    {
        $clientId = config('services.zoho.client_id');
        $redirectUri = route('zoho.callback');
        
        if (!$clientId) {
            return view('zoho.setup', [
                'error' => 'ZOHO_CLIENT_ID not found in .env file',
                'authUrl' => null,
            ]);
        }

        $authUrl = 'https://accounts.zoho.com/oauth/v2/auth?' . http_build_query([
            'scope' => 'ZohoBooks.fullaccess.all',
            'client_id' => $clientId,
            'response_type' => 'code',
            'access_type' => 'offline',
            'redirect_uri' => $redirectUri,
            'prompt' => 'consent', // Force consent to get refresh token
        ]);

        return view('zoho.setup', [
            'error' => null,
            'authUrl' => $authUrl,
            'clientId' => $clientId,
            'redirectUri' => $redirectUri,
        ]);
    }

    /**
     * Handle Zoho OAuth callback and save refresh token
     */
    public function callback(Request $request)
    {
        $code = $request->get('code');
        $error = $request->get('error');
        $errorDescription = $request->get('error_description');

        // Handle errors from Zoho
        if ($error) {
            Log::error('Zoho OAuth callback error', [
                'error' => $error,
                'error_description' => $errorDescription,
            ]);

            return view('zoho.callback', [
                'success' => false,
                'error' => $error,
                'error_description' => $errorDescription,
                'refresh_token' => null,
                'saved' => false,
            ]);
        }

        // Check if authorization code exists
        if (!$code) {
            return view('zoho.callback', [
                'success' => false,
                'error' => 'missing_code',
                'error_description' => 'Authorization code not found in callback URL',
                'refresh_token' => null,
                'saved' => false,
            ]);
        }

        try {
            $clientId = config('services.zoho.client_id');
            $clientSecret = config('services.zoho.client_secret');
            $redirectUri = route('zoho.callback');

            if (!$clientId || !$clientSecret) {
                return view('zoho.callback', [
                    'success' => false,
                    'error' => 'missing_config',
                    'error_description' => 'ZOHO_CLIENT_ID or ZOHO_CLIENT_SECRET not found in .env file',
                    'refresh_token' => null,
                    'saved' => false,
                ]);
            }

            // Exchange authorization code for tokens
            $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
                'grant_type' => 'authorization_code',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'redirect_uri' => $redirectUri,
                'code' => $code,
            ]);

            $statusCode = $response->status();
            $responseData = $response->json();

            if (!$response->successful() || isset($responseData['error'])) {
                $errorMessage = $responseData['error'] ?? 'Unknown error';
                $errorDescription = $responseData['error_description'] ?? '';

                Log::error('Zoho token exchange failed', [
                    'status' => $statusCode,
                    'error' => $errorMessage,
                    'error_description' => $errorDescription,
                    'response' => $responseData,
                ]);

                return view('zoho.callback', [
                    'success' => false,
                    'error' => $errorMessage,
                    'error_description' => $errorDescription,
                    'refresh_token' => null,
                    'saved' => false,
                ]);
            }

            $refreshToken = $responseData['refresh_token'] ?? null;
            $accessToken = $responseData['access_token'] ?? null;

            if (!$refreshToken) {
                Log::warning('Zoho token exchange: refresh_token not found', [
                    'response' => $responseData,
                ]);

                return view('zoho.callback', [
                    'success' => false,
                    'error' => 'no_refresh_token',
                    'error_description' => 'Refresh token not found in response. Make sure to use access_type=offline and prompt=consent',
                    'refresh_token' => null,
                    'saved' => false,
                ]);
            }

            // Save refresh token to .env file
            $saved = $this->saveRefreshTokenToEnv($refreshToken);

            Log::info('Zoho token exchange successful', [
                'has_access_token' => !empty($accessToken),
                'has_refresh_token' => !empty($refreshToken),
                'saved_to_env' => $saved,
            ]);

            return view('zoho.callback', [
                'success' => true,
                'error' => null,
                'error_description' => null,
                'refresh_token' => $refreshToken,
                'access_token' => $accessToken,
                'saved' => $saved,
            ]);

        } catch (Exception $e) {
            Log::error('Zoho callback exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('zoho.callback', [
                'success' => false,
                'error' => 'exception',
                'error_description' => $e->getMessage(),
                'refresh_token' => null,
                'saved' => false,
            ]);
        }
    }

    /**
     * Save refresh token to .env file
     * 
     * @param string $refreshToken
     * @return bool
     */
    private function saveRefreshTokenToEnv(string $refreshToken): bool
    {
        try {
            $envPath = base_path('.env');
            
            if (!File::exists($envPath)) {
                Log::error('.env file not found');
                return false;
            }

            $envContent = File::get($envPath);
            
            // Check if ZOHO_REFRESH_TOKEN already exists
            if (preg_match('/^ZOHO_REFRESH_TOKEN=.*$/m', $envContent)) {
                // Update existing
                $envContent = preg_replace(
                    '/^ZOHO_REFRESH_TOKEN=.*$/m',
                    'ZOHO_REFRESH_TOKEN=' . $refreshToken,
                    $envContent
                );
            } else {
                // Add new
                $envContent .= "\nZOHO_REFRESH_TOKEN=" . $refreshToken . "\n";
            }

            File::put($envPath, $envContent);

            // Clear config cache
            \Artisan::call('config:clear');

            return true;

        } catch (Exception $e) {
            Log::error('Failed to save refresh token to .env', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}


