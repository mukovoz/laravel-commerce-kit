<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Contracts\View\View;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Site;

class EmulateController extends BigcommerceController
{
    public function __invoke(Site $site): View
    {
        abort_if(app()->isProduction(), 403, 'Emulation is not available in production.');
        abort_if($site->platform != Platform::BigCommerce, 404);

        try {
            return view('bigcommerce.emulate', compact('site'));
        } catch (\InvalidArgumentException $exception) {
            return view('extension-framework::emulate', compact('site'));
        }

    }
}
