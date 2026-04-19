<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify;

use Illuminate\Routing\Controller;

abstract class ShopifyController extends Controller
{
    /**
     * Verify the HMAC on an OAuth install callback.
     * All query params except 'hmac' are sorted and hashed.
     */
    protected function verifyOAuthHmac(array $params): bool
    {
        $hmac = $params['hmac'] ?? '';
        unset($params['hmac']);

        ksort($params);

        $expected = hash_hmac('sha256', http_build_query($params), config('platforms.shopify.api_secret'));

        return hash_equals($expected, $hmac);
    }

    /**
     * Verify the HMAC on an incoming webhook (X-Shopify-Hmac-Sha256 header).
     */
    protected function verifyWebhookHmac(string $rawBody, string $hmacHeader): bool
    {
        $expected = base64_encode(
            hash_hmac('sha256', $rawBody, config('platforms.shopify.api_secret'), true)
        );

        return hash_equals($expected, $hmacHeader);
    }

    /**
     * Verify and decode a Shopify App Bridge session token (HS256 JWT).
     */
    protected function verifySessionToken(string $jwt): ?array
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        $expected = rtrim(strtr(base64_encode(
            hash_hmac('sha256', "{$header}.{$payload}", config('platforms.shopify.api_secret'), true)
        ), '+/', '-_'), '=');

        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $decoded = base64_decode(str_pad(strtr($payload, '-_', '+/'), strlen($payload) % 4, '=', STR_PAD_RIGHT));

        return json_decode($decoded, true) ?: null;
    }

    protected function shopDomain(string $shop): string
    {
        // Normalize to bare myshopify domain
        $shop = preg_replace('#^https?://#', '', $shop);

        return rtrim($shop, '/');
    }
}
