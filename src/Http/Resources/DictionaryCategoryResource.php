<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;

/**
 * 字典分类资源转换类
 *
 * @mixin DictionaryCategory
 */
class DictionaryCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'category_key' => $this->category_key,
            'category_name' => $this->category_name,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
