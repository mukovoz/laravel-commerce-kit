<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Http\Resources\SiteResource;

class LoadController extends ShopifyController
{
    public function __invoke(Request $request): JsonResponse
    {
        return (new SiteResource($request->site()))->response();
    }
}
