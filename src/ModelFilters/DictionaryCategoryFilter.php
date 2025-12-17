<?php

namespace WeiJuKeJi\LaravelDictionary\ModelFilters;

use EloquentFilter\ModelFilter;

class DictionaryCategoryFilter extends ModelFilter
{
    public $relations = [];

    /**
     * 分类Key筛选
     */
    public function categoryKey(string $key)
    {
        return $this->where('category_key', 'like', "%{$key}%");
    }

    /**
     * 分类名称筛选
     */
    public function categoryName(string $name)
    {
        return $this->where('category_name', 'like', "%{$name}%");
    }

    /**
     * 父分类ID筛选
     */
    public function parent(int|string $parentId)
    {
        return $this->where('parent_id', (int)$parentId);
    }
}
