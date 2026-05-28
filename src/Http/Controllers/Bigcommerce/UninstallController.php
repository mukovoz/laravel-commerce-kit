<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppUninstalled;

class UninstallController extends BigcommerceController
{

    public function __invoke(Request $request): void
    {
        $site = $request->site();
        $site->update([
            'uninstalled_at' => now(),
        ]);
        AppUninstalled::dispatch($request->site());
    }
}
