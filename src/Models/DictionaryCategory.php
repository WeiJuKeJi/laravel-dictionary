<?php

namespace WeiJuKeJi\LaravelDictionary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use EloquentFilter\Filterable;

/**
 * 字典分类模型
 *
 * @property int $id
 * @property int|null $parent_id
 * @property string $category_key
 * @property string $category_name
 * @property int $sort_order
 */
class DictionaryCategory extends Model
{
    use Filterable;

    protected $table = 'dictionary_categories';

    protected $fillable = [
        'parent_id',
        'category_key',
        'category_name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 配置过滤器
     */
    public function modelFilter()
    {
        return $this->provideFilter(\WeiJuKeJi\LaravelDictionary\ModelFilters\DictionaryCategoryFilter::class);
    }

    /**
     * 获取子分类
     */
    public function children(): HasMany
    {
        return $this->hasMany(DictionaryCategory::class, 'parent_id');
    }

    /**
     * 获取父分类
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(DictionaryCategory::class, 'parent_id');
    }

    /**
     * 获取该分类下的字典项
     */
    public function items(): HasMany
    {
        return $this->hasMany(DictionaryItem::class, 'parent_key', 'category_key');
    }

    /**
     * 递归获取所有子分类（包括自己）
     */
    public function getAllDescendants(): array
    {
        $descendants = [$this];

        foreach ($this->children as $child) {
            $descendants = array_merge($descendants, $child->getAllDescendants());
        }

        return $descendants;
    }
}
