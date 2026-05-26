<?php

namespace Lantera\ExtensionFramework\Events\Shopify;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lantera\ExtensionFramework\Models\Site;

class Activity
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Site $site) {}
}
