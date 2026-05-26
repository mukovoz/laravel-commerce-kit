<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify\Webhooks;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\ShopifyController;
use Lantera\ExtensionFramework\Models\Shopify\Site;

class AppsManagerController extends ShopifyController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'store_hash' => ['required', 'string'],
        ]);

        $site = Site::where('store_hash', $request->input('store_hash'))->firstOrFail();

        $site->update($request->only(['name', 'url', 'settings']));

        return response()->json($site->fresh());
    }
}
