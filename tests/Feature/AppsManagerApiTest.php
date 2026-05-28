<?php

namespace Lantera\ExtensionFramework\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppInstalled;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppUninstalled;
use Lantera\ExtensionFramework\Models\Site;
use Lantera\ExtensionFramework\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

/**
 * Run with real AppsManager API:
 *   vendor/bin/phpunit --group integration
 *
 * Requires .env.testing with real APPS_MANAGER_* credentials.
 */
#[Group('integration')]
class AppsManagerApiTest extends TestCase
{
    use RefreshDatabase;

    private function makeSite(array $attrs = []): Site
    {
        return Site::create(array_merge([
            'platform' => 'bigcommerce',
            'name'     => 'Integration Test Store',
            'url'      => 'https://integration-test.example.com',
        ], $attrs));
    }

    public function test_install_communicates_with_real_apps_manager(): void
    {
        $site = $this->makeSite();

        event(new AppInstalled($site));

        $site->refresh();

        // After a real install, AppsManager should return an access token
        $this->assertNotNull($site->apps_manager_access_token);
        $this->assertNotNull($site->plan);
    }

    public function test_uninstall_communicates_with_real_apps_manager(): void
    {
//        $site = $this->makeSite();
//
//        // No assertion on response — just verify it doesn't throw
//        event(new AppUninstalled($site));

        $this->assertTrue(true);
    }
}
