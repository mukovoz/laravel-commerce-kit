<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Http\Resources\SiteResource;

class LoadController extends BigcommerceController
{
    public function __invoke(Request $request): JsonResponse
    {
        return (new SiteResource($request->site()))->response();
    }
}
