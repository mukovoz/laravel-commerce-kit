<?php

namespace Lantera\ExtensionFramework\Observers;

use Lantera\ExtensionFramework\Models\Site;

class SiteObserver
{
    public function created(Site $site): void
    {
        $response = $site->appsManager->POST('/install', [
            'site_url'  => $site->url,
            'site_name' => $site->name,
            'ip'        => request()->ip(),
        ]);

        $site->syncFromAppsManager($response->json());
    }
}
