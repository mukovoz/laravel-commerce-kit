<?php

namespace Lantera\ExtensionFramework\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Lantera\ExtensionFramework\Enums\Platform;
use Lantera\ExtensionFramework\Services\AppsManager\ApiClient as AppsManagerClient;

/**
 * @property string $platform
 * @property AppsManagerClient $apps_manager;
 * @property mixed $appsManager
 * @property string $url
 * @property string $name;
 * @property-read string $domain
 */
class Site extends Model
{
    protected $table = 'extension_sites';

    protected $fillable = [
        'platform',
        'name',
        'url',
        'store_hash',
        'access_token',
        'settings',
        'uninstalled_at',
        'plan',
        'is_trial',
        'trial_start_at',
        'trial_end_at',
        'is_subscribed',
        'subscription_start_at',
        'subscription_end_at',
        'unsubscribed_at',
        'apps_manager_access_token',
    ];

    protected $casts = [
        'platform' => Platform::class,
        'settings' => 'array',
        'uninstalled_at' => 'datetime',
        'is_trial' => 'boolean',
        'trial_start_at' => 'datetime',
        'trial_end_at' => 'datetime',
        'is_subscribed' => 'boolean',
        'subscription_start_at' => 'datetime',
        'subscription_end_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected $hidden = ['access_token', 'apps_manager_access_token'];

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, mixed $value): static
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->update(['settings' => $settings]);

        return $this;
    }

    public function getSettings(): array
    {
        return $this->settings ?? [];
    }

    public function isInstalled(): bool
    {
        return $this->uninstalled_at === null;
    }

    public function isSubscribed(): bool
    {
        return $this->is_subscribed && $this->unsubscribed_at === null;
    }

    public function isTrial(): bool
    {
        return $this->is_trial && $this->trial_end_at !== null && $this->trial_end_at->isFuture();
    }

    public function isActive(): bool
    {
        return $this->isInstalled() && ($this->isTrial() || $this->isSubscribed());
    }

    public function scopeInstalled(Builder $query): Builder
    {
        return $query->whereNull('uninstalled_at');
    }

    public function scopePlatform(Builder $query, Platform $platform): Builder
    {
        return $query->where('platform', $platform->value);
    }

    public function syncFromAppsManager(array $data): void
    {
        $this->update([
            'apps_manager_access_token' => $data['access_token'] ?? null,
            'plan' => $data['plan']['code'] ?? 'default',
            'is_trial' => $data['is_trial'] ?? false,
            'trial_start_at' => $data['trial_start_at'] ?? null,
            'trial_end_at' => $data['trial_end_at'] ?? null,
            'is_subscribed' => $data['is_subscribed'] ?? false,
            'subscription_start_at' => $data['subscription_start_at'] ?? null,
            'subscription_end_at' => $data['subscription_end_at'] ?? null,
        ]);
    }

    public function getDomainAttribute(): string
    {
        return parse_url($this->url, PHP_URL_HOST);
    }

    public function getAppsManagerAttribute(): AppsManagerClient
    {
        return new AppsManagerClient(
            baseUrl: config('platforms.apps_manager.base_url') . "api/" . config('platforms.apps_manager.application_id'),
            applicationSecretKey: config('platforms.apps_manager.application_secret'),
            site: $this
        );
    }
}
