<?php

namespace WeiJuKeJi\LaravelDictionary;

use Illuminate\Support\ServiceProvider;
use WeiJuKeJi\LaravelDictionary\Services\DictionaryService;

class DictionaryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 加载路由
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // 加载迁移
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // 发布配置文件
        $this->publishes([
            __DIR__.'/../config/dictionary.php' => config_path('dictionary.php'),
        ], 'dictionary-config');

        // 发布迁移文件
        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'dictionary-migrations');

        // 注册命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                \WeiJuKeJi\LaravelDictionary\Console\Commands\ExportDictionariesCommand::class,
                \WeiJuKeJi\LaravelDictionary\Console\Commands\DictionaryReseedCommand::class,
            ]);
        }
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        // 合并配置文件
        $this->mergeConfigFrom(
            __DIR__.'/../config/dictionary.php',
            'dictionary'
        );

        // 注册字典服务
        $this->app->singleton('dictionary', function ($app) {
            return new DictionaryService();
        });

        // 注册 Facade 别名
        $this->app->alias('dictionary', DictionaryService::class);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['dictionary'];
    }
}
