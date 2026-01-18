<?php

use Illuminate\Support\Facades\Route;
use WeiJuKeJi\LaravelDictionary\Http\Controllers\DictionaryController;

/*
 * Dictionary API Routes
 */

Route::middleware(config('dictionary.route_middleware', ['api']))
    ->prefix(config('dictionary.route_prefix', 'api/dictionaries'))
    ->name('dictionary.')
    ->group(function () {
        // 字典分类树
        Route::get('/tree', [DictionaryController::class, 'getTree'])->name('tree');

        // 根据分类键获取字典项
        Route::get('/items/by-key/{categoryKey}', [DictionaryController::class, 'getItemsByKey'])->name('items.by-key');

        // 字典分类 CRUD
        Route::get('/categories', [DictionaryController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [DictionaryController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{dictionaryCategory}', [DictionaryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{dictionaryCategory}', [DictionaryController::class, 'destroyCategory'])->name('categories.destroy');

        // 字典项 CRUD
        Route::get('/items', [DictionaryController::class, 'items'])->name('items.index');
        Route::post('/items', [DictionaryController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{dictionaryItem}', [DictionaryController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{dictionaryItem}', [DictionaryController::class, 'destroyItem'])->name('items.destroy');
    });

