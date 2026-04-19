<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify;

use Illuminate\Contracts\View\View;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Site;

class EmulateController extends ShopifyController
{
    public function __invoke(Site $site): View
    {
        abort_if(app()->isProduction(), 403, 'Emulation is not available in production.');

        abort_if($site->platform !== Platform::Shopify, 404);



        return view('extension-framework::shopify.emulate', compact('site'));
    }
}
