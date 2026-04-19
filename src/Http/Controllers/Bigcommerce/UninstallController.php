<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppUninstalled;
use Lantera\ExtensionFramework\Models\Site;

class UninstallController extends BigcommerceController
{
    public function __invoke(Request $request): JsonResponse
    {
        $jwt = $request->query('signed_payload_jwt') ?? $request->query('signed_payload');

        abort_if(!$jwt, 400, 'Missing signed payload.');

        $payload = $this->verifySignedPayload($jwt);

        abort_if(!$payload, 401, 'Invalid signed payload.');

        // JWT sub is "stores/{hash}"
        $storeHash = $this->storeHashFromContext($payload['sub'] ?? '');

        $site = Site::where('platform', Platform::BigCommerce->value)
            ->where('store_hash', $storeHash)
            ->first();

        if ($site) {
            $site->update(['uninstalled_at' => now()]);
            AppUninstalled::dispatch($site);
        }

        return response()->json(['uninstalled' => true]);
    }
}
