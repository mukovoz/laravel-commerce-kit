<?php

namespace Lantera\ExtensionFramework;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Lantera\ExtensionFramework\Console\Commands\InstallCommand;
use Lantera\ExtensionFramework\Console\Commands\SitesCommand;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppInstalled as BigcommerceAppInstalled;
use Lantera\ExtensionFramework\Events\Bigcommerce\AppUninstalled as BigcommerceAppUninstalled;
use Lantera\ExtensionFramework\Events\Shopify\AppInstalled as ShopifyAppInstalled;
use Lantera\ExtensionFramework\Events\Shopify\AppUninstalled as ShopifyAppUninstalled;
use Lantera\ExtensionFramework\Http\Middleware\VerifyAppsManagerSecret;
use Lantera\ExtensionFramework\Http\Middleware\VerifyBigcommercePayload;
use Lantera\ExtensionFramework\Http\Middleware\VerifyShopifyPayload;
use Lantera\ExtensionFramework\Listeners\Bigcommerce\AppInstalled as BigcommerceAppInstalledListener;
use Lantera\ExtensionFramework\Listeners\Bigcommerce\AppUninstalled as BigcommerceAppUninstalledListener;
use Lantera\ExtensionFramework\Listeners\Shopify\AppInstalled as ShopifyAppInstalledListener;
use Lantera\ExtensionFramework\Listeners\Shopify\AppUninstalled as ShopifyAppUninstalledListener;
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
        Event::listen(BigcommerceAppInstalled::class, BigcommerceAppInstalledListener::class, PHP_INT_MAX);
        Event::listen(BigcommerceAppUninstalled::class, BigcommerceAppUninstalledListener::class, PHP_INT_MAX);
        Event::listen(ShopifyAppInstalled::class, ShopifyAppInstalledListener::class, PHP_INT_MAX);
        Event::listen(ShopifyAppUninstalled::class, ShopifyAppUninstalledListener::class, PHP_INT_MAX);

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'extension-framework');

        /**
         * Directive to use inside blade templates to load subscription page
         */

        Blade::directive('lantera_app_subscription', function (string $expression) {
            return "<?php echo view('extension-framework::subscription', ['site' => {$expression} ])->render(); ?>";
        });

        Route::aliasMiddleware('bigcommerce', VerifyBigcommercePayload::class);
        Route::aliasMiddleware('shopify', VerifyShopifyPayload::class);
        Route::aliasMiddleware('apps-manager', VerifyAppsManagerSecret::class);

        Request::macro('site', fn (): ?Site => $this->attributes->get('site'));

        Route::middleware('web')->group(function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/extension-framework.php');
        });

        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                SitesCommand::class,
            ]);

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
