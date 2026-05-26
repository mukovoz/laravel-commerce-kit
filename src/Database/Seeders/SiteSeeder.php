<?php

namespace Lantera\ExtensionFramework\Database\Seeders;

use Illuminate\Database\Seeder;
use Lantera\ExtensionFramework\Models\Site;

class SiteSeeder extends Seeder
{
    public function run(): void
    {
        $sites = [
            [
                'platform'                    => 'shopify',
                'name'                        => 'Demo Shopify Store',
                'url'                         => 'https://demo-shopify.myshopify.com',
                'store_hash'                  => 'demo-shopify.myshopify.com',
                'apps_manager_access_token'   => 'https://demo-shopify.myshopify.com',
            ],
            [
                'platform'                    => 'bigcommerce',
                'name'                        => 'Demo BigCommerce Store',
                'url'                         => 'https://trial.bigcommerce.com',
                'store_hash'                  => 'demo',
                'apps_manager_access_token'   => 'https://trial.bigcommerce.com',
            ],
            [
                'platform'                    => 'custom',
                'name'                        => 'Demo Custom Store',
                'url'                         => 'https://demo.example.com',
                'apps_manager_access_token'   => 'https://demo.example.com',
            ],
        ];

        foreach ($sites as $site) {
            Site::firstOrCreate(
                ['platform' => $site['platform'], 'url' => $site['url']],
                $site
            );
        }
    }
}
