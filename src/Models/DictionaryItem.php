<?php

namespace WeiJuKeJi\LaravelDictionary\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use EloquentFilter\Filterable;

/**
 * 字典项模型
 *
 * @property int $id
 * @property string $parent_key
 * @property string $item_key
 * @property string $item_value
 * @property int $sort_order
 * @property bool $is_enabled
 * @property string $is_enabled_text
 */
class DictionaryItem extends Model
{
    use Filterable;

    protected $table = 'dictionary_items';

    protected $fillable = [
        'parent_key',
        'item_key',
        'item_value',
        'sort_order',
        'is_enabled',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_enabled' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * 配置过滤器
     */
    public function modelFilter()
    {
        return $this->provideFilter(\WeiJuKeJi\LaravelDictionary\ModelFilters\DictionaryItemFilter::class);
    }

    /**
     * 获取所属分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DictionaryCategory::class, 'parent_key', 'category_key');
    }

    /**
     * 作用域 - 启用状态
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * 访问器 - 启用状态文本
     */
    public function getIsEnabledTextAttribute(): string
    {
        return $this->is_enabled ? '启用' : '停用';
    }
}
