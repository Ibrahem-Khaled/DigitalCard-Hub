<?php

return [
    /**
     * You can select either uat or sit for testing, and for production, use prod.
     * Please only include one of these environments (uat, sit, prod) in the environment configuration field.
     */
    'environment' => env('AMWALPAY_ENVIRONMENT', 'uat'),
    /**
     * Merchant ID
     */
    'merchant_id' => env('AMWALPAY_MERCHANT_ID'),
    /**
     * Terminal ID
     */
    'terminal_id' => env('AMWALPAY_TERMINAL_ID'),
    /**
     * Secret Key
     */
    'secret_key' => env('AMWALPAY_SECRET_KEY'),
    /**
     * Callback URL
     * Replace only the {example.com} with your site domain
     */
    'callback_url' => env('AMWALPAY_CALLBACK_URL'),

];
