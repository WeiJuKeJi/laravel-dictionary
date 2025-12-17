<?php

use Illuminate\Support\Facades\Route;
use WeiJuKeJi\LaravelDictionary\Http\Controllers\DictionaryController;

/*
 * Dictionary API Routes
 */

Route::middleware(config('dictionary.api.middleware', ['api']))
    ->prefix(config('dictionary.api.prefix', 'api/dictionaries'))
    ->name('dictionary.')
    ->group(function () {
        // 字典分类树
        Route::get('/tree', [DictionaryController::class, 'getTree'])->name('tree');

        // 根据分类键获取字典项
        Route::get('/items/by-key/{categoryKey}', [DictionaryController::class, 'getItemsByKey'])->name('items.by-key');

        // 字典分类 CRUD
        Route::apiResource('categories', DictionaryController::class, [
            'parameters' => ['categories' => 'dictionaryCategory'],
            'only' => ['index', 'store', 'update', 'destroy']
        ])->names([
            'index' => 'categories.index',
            'store' => 'categories.store',
            'update' => 'categories.update',
            'destroy' => 'categories.destroy',
        ])->where(['category' => '[0-9]+']);

        // 字典项 CRUD
        Route::apiResource('items', DictionaryController::class, [
            'parameters' => ['items' => 'dictionaryItem'],
            'only' => ['index', 'store', 'update', 'destroy']
        ])->names([
            'index' => 'items.index',
            'store' => 'items.store',
            'update' => 'items.update',
            'destroy' => 'items.destroy',
        ])->where(['item' => '[0-9]+']);

        // 映射分类方法
        Route::get('/categories', [DictionaryController::class, 'categories'])->name('categories.index');
        Route::post('/categories', [DictionaryController::class, 'storeCategory'])->name('categories.store');
        Route::put('/categories/{dictionaryCategory}', [DictionaryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/categories/{dictionaryCategory}', [DictionaryController::class, 'destroyCategory'])->name('categories.destroy');

        // 映射字典项方法
        Route::get('/items', [DictionaryController::class, 'items'])->name('items.index');
        Route::post('/items', [DictionaryController::class, 'storeItem'])->name('items.store');
        Route::put('/items/{dictionaryItem}', [DictionaryController::class, 'updateItem'])->name('items.update');
        Route::delete('/items/{dictionaryItem}', [DictionaryController::class, 'destroyItem'])->name('items.destroy');
    });
