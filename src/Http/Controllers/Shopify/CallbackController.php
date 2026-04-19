<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Shopify;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Events\Shopify\AppInstalled;
use Lantera\ExtensionFramework\Http\Resources\SiteResource;
use Lantera\ExtensionFramework\Models\Site;

class CallbackController extends ShopifyController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'shop'      => ['required', 'string'],
            'code'      => ['required', 'string'],
            'hmac'      => ['required', 'string'],
            'state'     => ['required', 'string'],
            'timestamp' => ['required', 'string'],
        ]);

        abort_if(
            $request->query('state') !== session('shopify_oauth_state'),
            403,
            'Invalid OAuth state.'
        );

        session()->forget('shopify_oauth_state');

        abort_if(!$this->verifyOAuthHmac($request->query()), 401, 'Invalid HMAC signature.');

        $shop = $this->shopDomain($request->query('shop'));

        try {
            $response = Http::post("https://{$shop}/admin/oauth/access_token", [
                'client_id'     => config('platforms.shopify.api_key'),
                'client_secret' => config('platforms.shopify.api_secret'),
                'code'          => $request->query('code'),
            ])->throw();
        } catch (RequestException $e) {
            abort(502, 'Failed to retrieve access token from Shopify.');
        }

        $data = $response->json();

        $site = Site::updateOrCreate(
            [
                'platform'   => Platform::Shopify->value,
                'store_hash' => $shop,
            ],
            [
                'name'           => $shop,
                'url'            => "https://{$shop}",
                'access_token'   => $data['access_token'],
                'uninstalled_at' => null,
            ]
        );

        AppInstalled::dispatch($site);

        return (new SiteResource($site))
            ->response()
            ->setStatusCode($site->wasRecentlyCreated ? 201 : 200);
    }
}
