<?php

namespace Lantera\ExtensionFramework\Tests;

use Dotenv\Dotenv;
use Lantera\ExtensionFramework\ExtensionFrameworkServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        $envFile = __DIR__ . '/../.env.testing';
        if (file_exists($envFile)) {
            Dotenv::createImmutable(dirname($envFile), '.env.testing')->safeLoad();
        }

        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ExtensionFrameworkServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Use real credentials from .env.testing if present, otherwise fall back to fakes
        $app['config']->set('platforms.apps_manager', [
            'base_url'           => env('APPS_MANAGER_BASE_URL', 'https://apps-manager.test/'),
            'application_id'     => env('APPS_MANAGER_APPLICATION_ID', 'test-app'),
            'application_secret' => env('APPS_MANAGER_APPLICATION_SECRET_KEY', 'secret'),
        ]);
    }
}
