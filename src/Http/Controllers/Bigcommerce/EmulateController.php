<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Contracts\View\View;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Site;

class EmulateController extends BigcommerceController
{
    public function __invoke(Site $site): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
    {
        abort_if(app()->isProduction(), 403, 'Emulation is not available in production.');
        abort_if($site->platform != Platform::BigCommerce, 404);

        session()->put('current_site_id', $site->id);
//        redirect('bigcommerce.load',compact('site'));
//        return view('extension-framework::bigcommerce.emulate', compact('site'));
        return redirect(route('bigcommerce.load'));

    }
}
