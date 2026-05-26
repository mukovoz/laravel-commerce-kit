<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppLoaded;

class LoadController extends BigcommerceController
{
    public function __invoke(Request $request): View
    {
        $site = $request->site();

        AppLoaded::dispatch($site);

        return view('extension-framework::bigcommerce.load', compact('site'));
    }
}
