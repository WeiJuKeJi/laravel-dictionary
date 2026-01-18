<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;

/**
 * 字典树节点资源转换类
 *
 * @mixin DictionaryCategory
 */
class TreeNodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $hasChildren = isset($this->children) &&
                       (is_countable($this->children) ? count($this->children) > 0 : !empty($this->children));

        return [
            'id' => $this->id,
            'key' => $this->category_key,
            'label' => $this->category_name,
            'count' => $this->items_count ?? 0,
            'children' => $this->when(
                $hasChildren,
                TreeNodeResource::collection($this->children)
            ),
        ];
    }
}
