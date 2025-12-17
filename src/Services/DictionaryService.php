<?php

namespace WeiJuKeJi\LaravelDictionary\Services;

use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;
use Illuminate\Support\Collection;

/**
 * 字典服务类
 */
class DictionaryService
{
    /**
     * 获取完整的字典分类树
     */
    public function getTree(): Collection
    {
        // 获取所有分类并按 sort_order 排序
        $categories = DictionaryCategory::with(['children' => function ($query) {
            $query->orderBy('sort_order');
        }])->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();

        // 递归加载所有子分类
        $this->loadChildrenRecursively($categories);

        // 添加根节点
        $root = collect([
            (object)[
                'id' => 'root',
                'category_key' => 'root',
                'category_name' => '根节点',
                'children' => $categories,
            ]
        ]);

        return $root;
    }

    /**
     * 递归加载子分类
     */
    protected function loadChildrenRecursively(Collection $categories): void
    {
        foreach ($categories as $category) {
            if ($category->children->isNotEmpty()) {
                $this->loadChildrenRecursively($category->children);
            }
        }
    }


    /**
     * 保存字典项（新增或编辑）
     */
    public function saveItem(array $data): DictionaryItem
    {
        // 检查同一 parent_key 下是否存在相同的 item_key
        if (isset($data['id'])) {
            // 编辑模式，排除自己
            $exists = DictionaryItem::where('parent_key', $data['parent_key'])
                ->where('item_key', $data['item_key'])
                ->where('id', '!=', $data['id'])
                ->exists();
        } else {
            // 新增模式
            $exists = DictionaryItem::where('parent_key', $data['parent_key'])
                ->where('item_key', $data['item_key'])
                ->exists();
        }

        if ($exists) {
            throw new \Exception('该分类下已存在相同的 key 值');
        }

        // 使用 updateOrCreate 来处理新增和更新
        return DictionaryItem::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'parent_key' => $data['parent_key'],
                'item_key' => $data['item_key'] ?? $data['key'],
                'item_value' => $data['item_value'] ?? $data['value'],
                'sort_order' => $data['sort_order'] ?? 0,
                'is_enabled' => $data['is_enabled'] ?? true,
            ]
        );
    }

    /**
     * 删除字典项
     */
    public function deleteItem(string $id): bool
    {
        $item = DictionaryItem::find($id);

        if (!$item) {
            throw new \Exception('字典项不存在');
        }

        return $item->delete();
    }

    /**
     * 保存分类（新增或编辑）
     */
    public function saveCategory(array $data): DictionaryCategory
    {
        // 检查 category_key 的唯一性
        if (isset($data['id'])) {
            // 编辑模式，排除自己
            $exists = DictionaryCategory::where('category_key', $data['category_key'])
                ->where('id', '!=', $data['id'])
                ->exists();
        } else {
            // 新增模式
            $exists = DictionaryCategory::where('category_key', $data['category_key'])->exists();
        }

        if ($exists) {
            throw new \Exception('该分类 key 已存在');
        }

        return DictionaryCategory::updateOrCreate(
            ['id' => $data['id'] ?? null],
            [
                'parent_id' => $data['parent_id'] ?? null,
                'category_key' => $data['category_key'],
                'category_name' => $data['category_name'],
                'sort_order' => $data['sort_order'] ?? 0,
            ]
        );
    }

    /**
     * 删除分类
     */
    public function deleteCategory(string $id): bool
    {
        $category = DictionaryCategory::find($id);

        if (!$category) {
            throw new \Exception('分类不存在');
        }

        // 检查是否有子分类
        if ($category->children()->exists()) {
            throw new \Exception('该分类下有子分类，无法删除');
        }

        // 检查是否有字典项
        if ($category->items()->exists()) {
            throw new \Exception('该分类下有字典项，无法删除');
        }

        return $category->delete();
    }
}
