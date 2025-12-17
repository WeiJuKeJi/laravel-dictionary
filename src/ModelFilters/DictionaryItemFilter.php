<?php

namespace WeiJuKeJi\LaravelDictionary\ModelFilters;

use EloquentFilter\ModelFilter;

class DictionaryItemFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 父分类Key筛选
     */
    public function parentKey(string $key)
    {
        return $this->where('parent_key', $key);
    }

    /**
     * 字典项Key筛选
     */
    public function itemKey(string $key)
    {
        return $this->where('item_key', 'like', "%{$key}%");
    }

    /**
     * 字典项值筛选
     */
    public function itemValue(string $value)
    {
        return $this->where('item_value', 'like', "%{$value}%");
    }

    /**
     * 启用状态筛选
     */
    public function isEnabled(bool|string $enabled)
    {
        $isEnabled = is_bool($enabled) ? $enabled : ($enabled === 'true' || $enabled === '1');
        return $this->where('is_enabled', $isEnabled);
    }
}
