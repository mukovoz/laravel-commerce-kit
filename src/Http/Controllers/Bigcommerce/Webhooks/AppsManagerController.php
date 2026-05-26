<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\Webhooks;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\BigcommerceController;

class AppsManagerController extends BigcommerceController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->site->syncFromAppsManager($request->all());


        Log::info('AppsManagerController invoked', $request->all());
        return response()->json($request->site);
    }
}
