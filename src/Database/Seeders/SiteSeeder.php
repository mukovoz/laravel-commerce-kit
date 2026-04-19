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
                'platform'   => 'shopify',
                'name'       => 'Demo Shopify Store',
                'url'        => 'https://demo-shopify.myshopify.com',
                'store_hash' => 'demo-shopify.myshopify.com',
            ],
            [
                'platform'   => 'bigcommerce',
                'name'       => 'Demo BigCommerce Store',
                'url'        => 'https://store-demo.mybigcommerce.com',
                'store_hash' => 'demo',
            ],
            [
                'platform' => 'custom',
                'name'     => 'Demo Custom Store',
                'url'      => 'https://demo.example.com',
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
