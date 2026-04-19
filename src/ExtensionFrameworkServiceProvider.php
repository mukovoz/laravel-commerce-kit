<?php

namespace Lantera\ExtensionFramework;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lantera\ExtensionFramework\Http\Middleware\VerifyBigcommercePayload;
use Lantera\ExtensionFramework\Http\Middleware\VerifyShopifyPayload;
use Lantera\ExtensionFramework\Models\Site;

class ExtensionFrameworkServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/platforms.php',
            'platforms'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'extension-framework');

        Route::aliasMiddleware('bigcommerce', VerifyBigcommercePayload::class);
        Route::aliasMiddleware('shopify', VerifyShopifyPayload::class);

        Request::macro('site', fn (): ?Site => $this->attributes->get('site'));

        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/bigcommerce.php');
            $this->loadRoutesFrom(__DIR__.'/../routes/shopify.php');
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/platforms.php' => config_path('platforms.php'),
            ], 'lantera-config');

            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'lantera-migrations');

            $this->publishes([
                __DIR__.'/../database/seeders/' => database_path('seeders'),
            ], 'lantera-seeders');
        }
    }
}
