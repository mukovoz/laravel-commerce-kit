<?php

namespace Lantera\ExtensionFramework\Models\Bigcommerce;

use Illuminate\Database\Eloquent\Builder;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Models\Site as BaseSite;
use Lantera\ExtensionFramework\Services\Bigcommerce\ApiClient;

class Site extends BaseSite
{
    protected static function booted(): void
    {
        static::addGlobalScope('platform', function (Builder $query) {
            $query->where('platform', Platform::BigCommerce->value);
        });
    }

    protected $attributes = [
        'platform' => Platform::BigCommerce,
    ];

    public function api(): ApiClient
    {
        return new ApiClient($this->store_hash, $this->access_token);
    }
}
