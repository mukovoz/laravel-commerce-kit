<?php
//
//namespace Lantera\ExtensionFramework\Tests\Feature;
//
//use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Support\Facades\Http;
//use Lantera\ExtensionFramework\Events\Bigcommerce\AppInstalled as BigcommerceAppInstalled;
//use Lantera\ExtensionFramework\Events\Bigcommerce\AppUninstalled as BigcommerceAppUninstalled;
//use Lantera\ExtensionFramework\Events\Shopify\AppInstalled as ShopifyAppInstalled;
//use Lantera\ExtensionFramework\Events\Shopify\AppUninstalled as ShopifyAppUninstalled;
//use Lantera\ExtensionFramework\Models\Site;
//use Lantera\ExtensionFramework\Tests\TestCase;
//
//class AppsManagerListenerTest extends TestCase
//{
//    use RefreshDatabase;
//
//    private function makeSite(array $attrs = []): Site
//    {
//        return Site::create(array_merge([
//            'platform' => 'bigcommerce',
//            'name'     => 'Test Store',
//            'url'      => 'https://store.example.com',
//        ], $attrs));
//    }
//
//    private function appsManagerInstallResponse(array $overrides = []): array
//    {
//        return array_merge([
//            'access_token'          => 'am-token-123',
//            'plan'                  => ['code' => 'pro'],
//            'is_trial'              => true,
//            'trial_start_at'        => '2024-01-01',
//            'trial_end_at'          => '2024-01-31',
//            'is_subscribed'         => false,
//            'subscription_start_at' => null,
//            'subscription_end_at'   => null,
//        ], $overrides);
//    }
//
//    // -------------------------------------------------------------------------
//    // Install
//    // -------------------------------------------------------------------------
//
//    public function test_install_posts_correct_payload_to_apps_manager(): void
//    {
//        Http::fake(['*' => Http::response($this->appsManagerInstallResponse())]);
//
//        $site = $this->makeSite();
//
//        event(new BigcommerceAppInstalled($site));
//
//        Http::assertSent(function ($request) use ($site) {
//            return str_ends_with($request->url(), '/install')
//                && $request['site_url'] === $site->url
//                && $request['site_name'] === $site->name
//                && isset($request['ip']);
//        });
//    }
//
//    public function test_install_syncs_apps_manager_response_to_site(): void
//    {
//        Http::fake(['*' => Http::response($this->appsManagerInstallResponse([
//            'access_token' => 'synced-token',
//            'plan'         => ['code' => 'enterprise'],
//            'is_trial'     => true,
//            'trial_end_at' => '2024-03-31',
//            'is_subscribed' => true,
//        ]))]);
//
//        $site = $this->makeSite();
//
//        event(new BigcommerceAppInstalled($site));
//
//        $site->refresh();
//        $this->assertEquals('synced-token', $site->apps_manager_access_token);
//        $this->assertEquals('enterprise', $site->plan);
//        $this->assertTrue($site->is_trial);
//        $this->assertTrue($site->is_subscribed);
//    }
//
//
//
//    // -------------------------------------------------------------------------
//    // Uninstall
//    // -------------------------------------------------------------------------
//
//    public function test_uninstall_posts_correct_payload_to_apps_manager(): void
//    {
//        Http::fake(['*' => Http::response([])]);
//
//        $site = $this->makeSite();
//
//        event(new BigcommerceAppUninstalled($site));
//
//        Http::assertSent(function ($request) use ($site) {
//            return str_ends_with($request->url(), '/uninstall')
//                && $request['site_url'] === $site->url;
//        });
//    }
//
//    public function test_uninstall_does_not_call_install_endpoint(): void
//    {
//        Http::fake(['*' => Http::response([])]);
//
//        $site = $this->makeSite();
//
//        event(new BigcommerceAppUninstalled($site));
//
//        Http::assertNotSent(fn ($req) => str_ends_with($req->url(), '/install'));
//    }
//
//    public function test_shopify_uninstall_posts_to_apps_manager(): void
//    {
//        Http::fake(['*' => Http::response([])]);
//
//        $site = $this->makeSite(['platform' => 'shopify']);
//
//        event(new ShopifyAppUninstalled($site));
//
//        Http::assertSent(fn ($req) =>
//            str_ends_with($req->url(), '/uninstall') && $req['site_url'] === $site->url
//        );
//    }
//}
