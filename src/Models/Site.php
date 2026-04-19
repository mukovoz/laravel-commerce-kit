<?php

namespace Lantera\ExtensionFramework\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lantera\ExtensionFramework\Enums\Platform;

/**
 * @property string $platform
 */
class Site extends Model
{
    protected $fillable = [
        'platform',
        'name',
        'url',
        'store_hash',
        'access_token',
        'uninstalled_at',
    ];

    protected $casts = [
        'platform'       => Platform::class,
        'uninstalled_at' => 'datetime',
    ];

    protected $hidden = ['access_token'];

    public function isInstalled(): bool
    {
        return $this->uninstalled_at === null;
    }

    public function scopeInstalled(Builder $query): Builder
    {
        return $query->whereNull('uninstalled_at');
    }

    public function scopePlatform(Builder $query, Platform $platform): Builder
    {
        return $query->where('platform', $platform->value);
    }

}
