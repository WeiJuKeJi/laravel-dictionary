<?php

namespace WeiJuKeJi\LaravelDictionary\Exceptions;

use Exception;

/**
 * 字典异常类
 */
class DictionaryException extends Exception
{
    /**
     * 重复的键值
     */
    public static function duplicateKey(string $key): self
    {
        return new self("该分类下已存在相同的 key 值: {$key}");
    }

    /**
     * 分类不存在
     */
    public static function categoryNotFound(string $id): self
    {
        return new self("分类不存在: {$id}");
    }

    /**
     * 分类下有子分类
     */
    public static function categoryHasChildren(): self
    {
        return new self('该分类下有子分类，无法删除');
    }

    /**
     * 分类下有字典项
     */
    public static function categoryHasItems(): self
    {
        return new self('该分类下有字典项，无法删除');
    }

    /**
     * 字典项不存在
     */
    public static function itemNotFound(string $id): self
    {
        return new self("字典项不存在: {$id}");
    }
}
