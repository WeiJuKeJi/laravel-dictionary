<?php

namespace WeiJuKeJi\LaravelDictionary\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $type, string $code)
 * @method static string|null getName(string $type, string $code)
 * @method static array getList(string $type)
 * @method static bool validate(string $type, string $code)
 * @method static array getBatch(string $type, array $codes)
 * @method static array getTree(string $type)
 * @method static array pluck(string $type)
 * @method static array search(string $type, string $keyword)
 * @method static void refreshCache(string|null $type = null)
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
