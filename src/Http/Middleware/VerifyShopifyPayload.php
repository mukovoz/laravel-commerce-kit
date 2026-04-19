<?php

namespace Lantera\ExtensionFramework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Models\Shopify\Site;
use Symfony\Component\HttpFoundation\Response;

class VerifyShopifyPayload
{
    public function handle(Request $request, Closure $next): Response
    {
        $jwt = $request->bearerToken()
            ?? $request->query('id_token')
            ?? $request->query('session');

        abort_if(!$jwt, 400, 'Missing session token.');

        $payload = $this->verifySessionToken($jwt);

        abort_if(!$payload, 401, 'Invalid session token.');

        $shop = $this->shopDomain($payload['dest'] ?? '');

        $site = Site::where('store_hash', $shop)
            ->installed()
            ->firstOrFail();

        $request->attributes->set('site', $site);
        app()->instance(Site::class, $site);

        return $next($request);
    }

    private function verifySessionToken(string $jwt): ?array
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

    private function shopDomain(string $shop): string
    {
        return rtrim(preg_replace('#^https?://#', '', $shop), '/');
    }
}
