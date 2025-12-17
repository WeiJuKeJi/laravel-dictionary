<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;

/**
 * 字典项资源转换类
 *
 * @mixin DictionaryItem
 */
class DictionaryItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_key' => $this->parent_key,
            'item_key' => $this->item_key,
            'item_value' => $this->item_value,
            'sort_order' => $this->sort_order,
            'is_enabled' => $this->is_enabled,
            'is_enabled_text' => $this->is_enabled_text,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
