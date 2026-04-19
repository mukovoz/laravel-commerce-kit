<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Events\Shopify\AppUninstalled;
use Lantera\ExtensionFramework\Models\Site;

class UninstallController extends ShopifyController
{
    public function __invoke(Request $request): JsonResponse
    {
        $hmacHeader = $request->header('X-Shopify-Hmac-Sha256', '');

        abort_if(
            !$this->verifyWebhookHmac($request->getContent(), $hmacHeader),
            401,
            'Invalid webhook HMAC.'
        );

        $payload = $request->json()->all();
        $shop    = $this->shopDomain($payload['myshopify_domain'] ?? $payload['domain'] ?? '');

        abort_if(!$shop, 400, 'Missing shop domain in webhook payload.');

        $site = Site::where('platform', Platform::Shopify->value)
            ->where('store_hash', $shop)
            ->first();

        if ($site) {
            $site->update(['uninstalled_at' => now()]);
            AppUninstalled::dispatch($site);
        }

        return response()->json(['uninstalled' => true]);
    }
}
