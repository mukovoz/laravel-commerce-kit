<?php

namespace Lantera\ExtensionFramework\Listeners;

abstract class AppInstalled
{
    public function handle(object $event): void
    {
        $site = $event->site;

        $response = $site->appsManager->POST('/install', [
            'site_url'  => $site->url,
            'site_name' => $site->name,
            'ip'        => request()->ip(),
        ]);

        $site->syncFromAppsManager($response->json());
    }
}
