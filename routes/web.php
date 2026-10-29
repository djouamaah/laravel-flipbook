<?php

use Djouamaah\Flipbook\Http\Controllers\FlipbookController;
use Illuminate\Support\Facades\Route;

Route::middleware(config('flipbook.middleware'))
    ->prefix(config('flipbook.prefix'))
    ->name('flipbook.')
    ->controller(FlipbookController::class)
    ->group(function () {
        Route::middleware(config('flipbook.manage_middleware'))->group(function () {
            Route::get('create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::delete('{flipbook}', 'destroy')->name('destroy');
        });

        Route::get('/', 'index')->name('index');
        Route::get('{flipbook}', 'show')->name('show');
        Route::get('{flipbook}/embed', 'embed')->name('embed');
        Route::get('{flipbook}/document', 'document')->name('document');
    });
