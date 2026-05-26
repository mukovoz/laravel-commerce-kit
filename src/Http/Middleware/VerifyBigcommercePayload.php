<?php

namespace Lantera\ExtensionFramework\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Models\Bigcommerce\Site;
use Symfony\Component\HttpFoundation\Response;

class VerifyBigcommercePayload
{
    private const string SESSION_KEY = 'current_site_id';

    public function handle(Request $request, Closure $next): Response
    {
        $site = $this->siteFromSession($request);
        $jwt  = $request->query('signed_payload_jwt') ?? $request->query('signed_payload');

        if (!$site || $jwt) {
            abort_if(!$jwt, 400, 'Missing signed payload.');

            $payload = $this->verifyJwt($jwt);

            abort_if(!$payload, 401, 'Invalid signed payload.');

            $storeHash = $this->storeHashFromContext($payload['sub'] ?? '');

            $site = Site::where('store_hash', $storeHash)
                ->installed()
                ->firstOrFail();

            $request->session()->put(self::SESSION_KEY, $site->id);
        }

        $request->attributes->set('site', $site);
        app()->instance(Site::class, $site);

        return $next($request);
    }

    private function siteFromSession(Request $request): ?Site
    {
        $id = $request->session()->get(self::SESSION_KEY);

        return $id ? Site::installed()->find($id) : null;
    }

    private function verifyJwt(string $jwt): ?array
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

    private function storeHashFromContext(string $context): string
    {
        return explode('/', ltrim($context, '/'))[1] ?? $context;
    }
}
