<?php

namespace Lantera\ExtensionFramework\Listeners;

abstract class AppUninstalled
{
    public function handle(object $event): void
    {
        $site = $event->site;

        $site->appsManager->POST('/uninstall', [
            'site_url' => $site->url,
        ]);
    }
}