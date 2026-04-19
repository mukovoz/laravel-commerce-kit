<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InstallController extends ShopifyController
{
    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate(['shop' => ['required', 'string']]);

        $shop  = $this->shopDomain($request->query('shop'));
        $state = bin2hex(random_bytes(16));

        session(['shopify_oauth_state' => $state]);

        $authUrl = "https://{$shop}/admin/oauth/authorize?" . http_build_query([
            'client_id'    => config('platforms.shopify.api_key'),
            'scope'        => config('platforms.shopify.scopes'),
            'redirect_uri' => route('shopify.callback'),
            'state'        => $state,
        ]);

        return redirect($authUrl);
    }
}
