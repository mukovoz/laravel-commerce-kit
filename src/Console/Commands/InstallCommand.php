<?php

namespace Lantera\ExtensionFramework\Console\Commands;

use Illuminate\Console\Command;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Bigcommerce\Site as BigcommerceSite;
use Lantera\ExtensionFramework\Models\Shopify\Site as ShopifySite;

class InstallCommand extends Command
{
    protected $signature = 'extension:install';
    protected $description = 'Emulate a platform app installation and register with AppsManager (non-production only)';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('This command is not available in production.');
            return self::FAILURE;
        }

        $platformLabel = $this->choice('Which platform?', ['Shopify', 'BigCommerce']);
        $platform = $platformLabel === 'Shopify' ? Platform::Shopify : Platform::BigCommerce;

        $rawUrl = $this->ask(
            $platform === Platform::Shopify
                ? 'Store URL (e.g. myshop.myshopify.com)'
                : 'Store URL (e.g. store-abc123.mybigcommerce.com)'
        );

        $url = $this->normalizeUrl($rawUrl);
        $storeHash = $this->deriveStoreHash($platform, $url);
        $name = $this->deriveName($url);

        $siteClass = $platform === Platform::Shopify ? ShopifySite::class : BigcommerceSite::class;

        $existing = $siteClass::where('store_hash', $storeHash)->first();

        if ($existing) {
            $this->warn("Site already exists (ID: {$existing->id}). Skipping AppsManager registration.");
            $site = $existing;
        } else {
            $this->info('Creating site and registering with AppsManager...');
            $site = $siteClass::create([
                'platform'     => $platform->value,
                'name'         => $name,
                'url'          => $url,
                'store_hash'   => $storeHash,
                'access_token' => 'emulated_' . bin2hex(random_bytes(16)),
            ]);
            $this->info("Site created (ID: {$site->id}).");
        }

        $emulateRoute = $platform === Platform::Shopify ? 'shopify.emulate' : 'bigcommerce.emulate';
        $emulateUrl = route($emulateRoute, $site->id);

        $this->newLine();
        $this->components->twoColumnDetail('<fg=green>Emulate URL</>', "<fg=cyan;options=bold>{$emulateUrl}</>");
        $this->newLine();

        return self::SUCCESS;
    }

    private function normalizeUrl(string $url): string
    {
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }

        return rtrim($url, '/');
    }

    private function deriveStoreHash(Platform $platform, string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?? $url;

        if ($platform === Platform::BigCommerce) {
            if (preg_match('/^store-([a-z0-9]+)\.mybigcommerce\.com$/i', $host, $m)) {
                return $m[1];
            }
        }

        return $host;
    }

    private function deriveName(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST) ?? $url;

        return ucfirst(explode('.', $host)[0]);
    }
}