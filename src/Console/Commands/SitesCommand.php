<?php

namespace Lantera\ExtensionFramework\Console\Commands;

use Illuminate\Console\Command;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Site;

class SitesCommand extends Command
{
    protected $signature = 'extension:sites';
    protected $description = 'List all registered sites with their emulate URLs (non-production only)';

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('This command is not available in production.');
            return self::FAILURE;
        }

        $sites = Site::orderBy('platform')->orderBy('created_at')->get();

        if ($sites->isEmpty()) {
            $this->info('No sites registered yet. Run `php artisan extension:install` to add one.');
            return self::SUCCESS;
        }

        $rows = $sites->map(function (Site $site) {
            $emulateRoute = $site->platform === Platform::Shopify
                ? 'shopify.emulate'
                : 'bigcommerce.emulate';

            return [
                $site->id,
                $site->platform->label(),
                $site->name,
                $site->url,
                $site->isInstalled() ? '<fg=green>Installed</>' : '<fg=red>Uninstalled</>',
                route($emulateRoute, $site->id),
            ];
        })->toArray();

        $this->table(
            ['ID', 'Platform', 'Name', 'URL', 'Status', 'Emulate URL'],
            $rows
        );

        return self::SUCCESS;
    }
}