<?php

namespace Lantera\ExtensionFramework\Http\Controllers\Bigcommerce;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppInstalled;
use Lantera\ExtensionFramework\Http\Resources\SiteResource;
use Lantera\ExtensionFramework\Models\Site;

class InstallController extends BigcommerceController
{
    public function __invoke(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'scope' => ['required', 'string'],
            'context' => ['required', 'string'],
        ]);

        try {

            $response = Http::asForm()->post('https://login.bigcommerce.com/oauth2/token', [
                'client_id' => config('platforms.bigcommerce.client_id'),
                'client_secret' => config('platforms.bigcommerce.client_secret'),
                'code' => $request->query('code'),
                'scope' => $request->query('scope'),
                'grant_type' => 'authorization_code',
                'redirect_uri' => route('bigcommerce.install', [], true),
                'context' => $request->query('context'),
            ])->throw();
        } catch (RequestException $e) {
            abort(502, 'Failed to retrieve access token from BigCommerce: ' . $e->getMessage());
        }

        $data = $response->json();
        $storeHash = $this->storeHashFromContext($request->query('context'));

        $site = Site::updateOrCreate(
            [
                'platform' => Platform::BigCommerce->value,
                'store_hash' => $storeHash,
            ],
            [
                'name' => "Store {$storeHash}",
                'url' => "https://store-{$storeHash}.mybigcommerce.com",
                'access_token' => $data['access_token'],
                'uninstalled_at' => null,
            ]
        );

        AppInstalled::dispatch($site);

        return (new SiteResource($site))
            ->response()
            ->setStatusCode($site->wasRecentlyCreated ? 201 : 200);
    }
}
