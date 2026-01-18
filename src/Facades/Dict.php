<?php

namespace WeiJuKeJi\LaravelDictionary\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Support\Collection getTree()
 * @method static \Illuminate\Support\Collection getItemsByKey(string $categoryKey, bool $enabledOnly = true)
 * @method static string|null getItemValue(string $categoryKey, string $itemKey)
 * @method static void refreshCache(string|null $categoryKey = null)
 *
 * @see \WeiJuKeJi\LaravelDictionary\Services\DictionaryService
 */
class Dict extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'dictionary';
    }
}
