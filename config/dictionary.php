<?php

return [
    'name' => 'Dictionary',

    /*
    |--------------------------------------------------------------------------
    | 数据库表名
    |--------------------------------------------------------------------------
    |
    | 字典数据表名称，可自定义以避免与现有表冲突
    |
    */
    'table_categories' => env('DICTIONARY_TABLE_CATEGORIES', 'dictionary_categories'),
    'table_items' => env('DICTIONARY_TABLE_ITEMS', 'dictionary_items'),

    /*
    |--------------------------------------------------------------------------
    | 缓存配置
    |--------------------------------------------------------------------------
    |
    | 字典缓存相关配置
    |
    */
    'cache' => [
        // 是否启用缓存
        'enabled' => env('DICTIONARY_CACHE_ENABLED', true),

        // 缓存时间（秒）
        'ttl' => env('DICTIONARY_CACHE_TTL', 3600),

        // 缓存键前缀
        'prefix' => env('DICTIONARY_CACHE_PREFIX', 'dict:'),

        // 缓存驱动（null 使用默认驱动）
        'driver' => env('DICTIONARY_CACHE_DRIVER', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | API 路由配置
    |--------------------------------------------------------------------------
    |
    | 字典 API 路由相关配置
    |
    */
    'api' => [
        // 是否启用 API 路由
        'enabled' => env('DICTIONARY_API_ENABLED', true),

        // API 路由前缀
        'prefix' => env('DICTIONARY_API_PREFIX', 'api/dictionaries'),

        // API 中间件
        'middleware' => ['api'],
    ],
];
