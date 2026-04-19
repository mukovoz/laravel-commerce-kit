<?php

use Illuminate\Support\Facades\Route;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\EmulateController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\InstallController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\LoadController;
use Lantera\ExtensionFramework\Http\Controllers\Bigcommerce\UninstallController;

Route::prefix('bigcommerce')->name('bigcommerce.')->group(function () {
    Route::get('install', InstallController::class)->name('install');
    Route::get('uninstall', UninstallController::class)->name('uninstall');
    Route::get('emulate/{site}', EmulateController::class)->name('emulate');

    Route::middleware('bigcommerce')->group(function () {
        Route::get('load', LoadController::class)->name('load');
    });
});
