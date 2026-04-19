<?php

namespace Lantera\ExtensionFramework\Models\Shopify;

use Illuminate\Database\Eloquent\Builder;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Site as BaseSite;

class Site extends BaseSite
{
    protected static function booted(): void
    {
        static::addGlobalScope('platform', function (Builder $query) {
            $query->where('platform', Platform::Shopify->value);
        });
    }

    protected $attributes = [
        'platform' => Platform::Shopify,
    ];
}
