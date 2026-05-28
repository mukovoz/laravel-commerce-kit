<?php

use Illuminate\Support\Facades\Route;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\EmulateController as BigcommerceEmulateController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\InstallController as BigcommerceInstallController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\LoadController as BigcommerceLoadController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\UninstallController as BigcommerceUninstallController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\CallbackController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\EmulateController as ShopifyEmulateController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\InstallController as ShopifyInstallController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\LoadController as ShopifyLoadController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\Webhooks\AppsManagerController as BigcommerceAppsManagerController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\UninstallController as ShopifyUninstallController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\Webhooks\AppsManagerController as ShopifyAppsManagerController;


Route::prefix('bigcommerce')->name('bigcommerce.')->group(function () {
    Route::post('webhook/apps-manager', BigcommerceAppsManagerController::class)
        ->name('webhook.apps-manager')
        ->middleware('apps-manager')
        ->withoutMiddleware('web');

    Route::get('install', BigcommerceInstallController::class)->name('install');

    Route::get('emulate/{site}', BigcommerceEmulateController::class)->name('emulate');

    Route::middleware('bigcommerce')->group(function () {
        Route::get('load', BigcommerceLoadController::class)->name('load');
        Route::get('uninstall', BigcommerceUninstallController::class)->name('uninstall');
    });
});


Route::prefix('shopify')->name('shopify.')->group(function () {
    Route::post('webhook/apps-manager', ShopifyAppsManagerController::class)
        ->name('webhook.apps-manager')
        ->middleware('apps-manager')
        ->withoutMiddleware('web');

    Route::get('install', ShopifyInstallController::class)->name('install');
    Route::get('callback', CallbackController::class)->name('callback');

    Route::get('emulate/{site}', ShopifyEmulateController::class)->name('emulate');

    Route::middleware('shopify')->group(function () {
        Route::get('load', ShopifyLoadController::class)->name('load');
        Route::post('uninstall', ShopifyUninstallController::class)->name('uninstall');
    });
});
