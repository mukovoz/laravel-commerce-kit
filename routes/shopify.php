<?php

use Illuminate\Support\Facades\Route;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\CallbackController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\EmulateController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\InstallController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\LoadController;
use Lantera\ExtensionFramework\Http\Controllers\Shopify\UninstallController;

Route::prefix('shopify')->name('shopify.')->group(function () {
    Route::get('install', InstallController::class)->name('install');
    Route::get('callback', CallbackController::class)->name('callback');
    Route::post('uninstall', UninstallController::class)->name('uninstall');
    Route::get('emulate/{site}', EmulateController::class)->name('emulate');

    Route::middleware('shopify')->group(function () {
        Route::get('load', LoadController::class)->name('load');
    });
});
