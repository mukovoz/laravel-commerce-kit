<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Shopify
    |--------------------------------------------------------------------------
    */
    'shopify' => [
        'api_key' => env('SHOPIFY_API_KEY', ''),
        'api_secret' => env('SHOPIFY_API_SECRET', ''),
        'scopes' => env('SHOPIFY_SCOPES', 'read_products,write_products'),
        'webhook_secret' => env('SHOPIFY_WEBHOOK_SECRET', ''),
        'api_version' => env('SHOPIFY_API_VERSION', '2025-01'),
    ],

    /*
    |--------------------------------------------------------------------------
    | BigCommerce
    |--------------------------------------------------------------------------
    */
    'bigcommerce' => [
        'client_id' => env('BIGCOMMERCE_CLIENT_ID', ''),
        'client_secret' => env('BIGCOMMERCE_CLIENT_SECRET', ''),
        'callback_url' => env('BIGCOMMERCE_CALLBACK_URL', ''),
        'scopes' => env('BIGCOMMERCE_SCOPES', 'store_v2_products'),
        'api_version' => env('BIGCOMMERCE_API_VERSION', 'v2'),
        'app_id' => env('BIGCOMMERCE_APP_ID', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'api_key' => env('CUSTOM_API_KEY', ''),
        'api_secret' => env('CUSTOM_API_SECRET', ''),
        'webhook_secret' => env('CUSTOM_WEBHOOK_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Apps Manager
    |--------------------------------------------------------------------------
    */
    'apps_manager' => [
        'base_url' => env('APPS_MANAGER_BASE_URL', 'https://api.appsmanager.com/'),
        'script_url' => rtrim(env('APPS_MANAGER_SCRIPT_URL', 'https://api.appsmanager.com/script'), '/') . '/' . env('APPS_MANAGER_APPLICATION_ID', '') . "/",
        'application_id' => env('APPS_MANAGER_APPLICATION_ID', ''),
        'application_secret' => env('APPS_MANAGER_APPLICATION_SECRET_KEY', ''),
    ],

];
