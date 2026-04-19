<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Routing\Controller;

abstract class BigcommerceController extends Controller
{
    /**
     * Verify and decode a BigCommerce signed_payload_jwt (HS256).
     * Returns the decoded payload array, or null if verification fails.
     */
    protected function verifySignedPayload(string $jwt): ?array
    {
        $parts = explode('.', $jwt);

        if (count($parts) !== 3) {
            return null;
        }

        [$header, $payload, $signature] = $parts;

        $expected = rtrim(strtr(base64_encode(
            hash_hmac('sha256', "{$header}.{$payload}", config('platforms.bigcommerce.client_secret'), true)
        ), '+/', '-_'), '=');

        if (!hash_equals($expected, $signature)) {
            return null;
        }

        $decoded = base64_decode(str_pad(strtr($payload, '-_', '+/'), strlen($payload) % 4, '=', STR_PAD_RIGHT));

        return json_decode($decoded, true) ?: null;
    }

    protected function storeHashFromContext(string $context): string
    {
        // context is "stores/{hash}" or "stores/{hash}/..."
        return explode('/', ltrim($context, '/'))[1] ?? $context;
    }
}
