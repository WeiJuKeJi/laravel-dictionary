<?php

namespace WeiJuKeJi\LaravelDictionary\Services;

use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;
use WeiJuKeJi\LaravelDictionary\Exceptions\DictionaryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * 字典服务类
 */
class DictionaryService
{
    /**
     * 获取完整的字典分类树（优化 N+1 查询）
     */
    public function getTree(): Collection
    {
        // 使用缓存
        if (config('dictionary.cache_enabled')) {
            return $this->getCachedTree();
        }

        return $this->buildTreeFromDatabase();
    }

    /**
     * 从缓存获取树形结构
     */
    protected function getCachedTree(): Collection
    {
        $cacheKey = config('dictionary.cache_prefix') . 'tree';

        return Cache::remember(
            $cacheKey,
            config('dictionary.cache_ttl'),
            fn() => $this->buildTreeFromDatabase()
        );
    }

    /**
     * 从数据库构建树形结构（优化：一次性加载所有分类和字典项数量）
     */
    protected function buildTreeFromDatabase(): Collection
    {
        // 一次性加载所有分类，避免 N+1 查询
        $allCategories = DictionaryCategory::orderBy('sort_order')->get();

        // 一次性查询所有分类的字典项数量
        $itemCounts = DictionaryItem::select('parent_key', DB::raw('count(*) as count'))
            ->groupBy('parent_key')
            ->pluck('count', 'parent_key');

        // 为每个分类添加字典项数量
        $allCategories->each(function ($category) use ($itemCounts) {
            $category->items_count = $itemCounts->get($category->category_key, 0);
        });

        // 在内存中构建树形结构
        $tree = $this->buildTree($allCategories);

        // 添加根节点
        $root = collect([
            (object)[
                'id' => 'root',
                'category_key' => 'root',
                'category_name' => '根节点',
                'items_count' => 0,
                'children' => $tree,
            ]
        ]);

        return $root;
    }

    /**
     * 递归构建树形结构
     */
    protected function buildTree(Collection $categories, $parentId = null): Collection
    {
        return $categories
            ->where('parent_id', $parentId)
            ->map(function ($category) use ($categories) {
                $category->children = $this->buildTree($categories, $category->id);
                return $category;
            })
            ->values();
    }


    /**
     * 保存字典项（新增或编辑）
     */
    public function saveItem(array $data): DictionaryItem
    {
        return DB::transaction(function () use ($data) {
            // 处理自动生成字典键
            if (!isset($data['id']) && ($data['auto_generate_key'] ?? false)) {
                $data['item_key'] = $this->generateItemKey($data['parent_key']);
            }

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
                throw DictionaryException::duplicateKey($data['item_key']);
            }

            // 使用 updateOrCreate 来处理新增和更新
            $item = DictionaryItem::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'parent_key' => $data['parent_key'],
                    'item_key' => $data['item_key'] ?? $data['key'],
                    'item_value' => $data['item_value'] ?? $data['value'],
                    'sort_order' => $data['sort_order'] ?? 0,
                    'is_enabled' => $data['is_enabled'] ?? true,
                ]
            );

            // 清除相关缓存
            $this->clearItemCache($data['parent_key']);

            // 清除树形结构缓存（因为树中包含 count 字段）
            $this->clearTreeCache();

            return $item;
        });
    }

    /**
     * 自动生成字典键
     *
     * @param string $parentKey 父分类键
     * @return string 生成的字典键（数字）
     */
    protected function generateItemKey(string $parentKey): string
    {
        // 获取该分类下所有字典项
        $items = DictionaryItem::where('parent_key', $parentKey)
            ->pluck('item_key');

        // 过滤出纯数字的键
        $numericKeys = $items->filter(function ($key) {
            return is_numeric($key) && preg_match('/^\d+$/', $key);
        })->map(function ($key) {
            return (int)$key;
        });

        // 如果没有数字键，从 1 开始，否则在最大值基础上加 1
        return $numericKeys->isEmpty() ? '1' : (string)($numericKeys->max() + 1);
    }

    /**
     * 删除字典项
     */
    public function deleteItem(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $item = DictionaryItem::find($id);

            if (!$item) {
                throw DictionaryException::itemNotFound($id);
            }

            $parentKey = $item->parent_key;
            $result = $item->delete();

            // 清除相关缓存
            $this->clearItemCache($parentKey);

            // 清除树形结构缓存（因为树中包含 count 字段）
            $this->clearTreeCache();

            return $result;
        });
    }

    /**
     * 保存分类（新增或编辑）
     */
    public function saveCategory(array $data): DictionaryCategory
    {
        return DB::transaction(function () use ($data) {
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
                throw DictionaryException::duplicateKey($data['category_key']);
            }

            $category = DictionaryCategory::updateOrCreate(
                ['id' => $data['id'] ?? null],
                [
                    'parent_id' => $data['parent_id'] ?? null,
                    'category_key' => $data['category_key'],
                    'category_name' => $data['category_name'],
                    'sort_order' => $data['sort_order'] ?? 0,
                ]
            );

            // 清除树形结构缓存
            $this->clearTreeCache();

            return $category;
        });
    }

    /**
     * 删除分类
     */
    public function deleteCategory(string $id): bool
    {
        return DB::transaction(function () use ($id) {
            $category = DictionaryCategory::find($id);

            if (!$category) {
                throw DictionaryException::categoryNotFound($id);
            }

            // 检查是否有子分类
            if ($category->children()->exists()) {
                throw DictionaryException::categoryHasChildren();
            }

            // 检查是否有字典项
            if ($category->items()->exists()) {
                throw DictionaryException::categoryHasItems();
            }

            $result = $category->delete();

            // 清除树形结构缓存
            $this->clearTreeCache();

            return $result;
        });
    }

    /**
     * 根据分类键获取字典项（带缓存）
     */
    public function getItemsByKey(string $categoryKey, bool $enabledOnly = true): Collection
    {
        if (!config('dictionary.cache_enabled')) {
            return $this->fetchItems($categoryKey, $enabledOnly);
        }

        $cacheKey = config('dictionary.cache_prefix') . "items:{$categoryKey}:" . ($enabledOnly ? 'enabled' : 'all');

        return Cache::remember(
            $cacheKey,
            config('dictionary.cache_ttl'),
            fn() => $this->fetchItems($categoryKey, $enabledOnly)
        );
    }

    /**
     * 从数据库获取字典项
     */
    protected function fetchItems(string $categoryKey, bool $enabledOnly): Collection
    {
        $query = DictionaryItem::where('parent_key', $categoryKey);

        if ($enabledOnly) {
            $query->enabled();
        }

        return $query->orderBy('sort_order')->get();
    }

    /**
     * 获取字典项的值
     */
    public function getItemValue(string $categoryKey, string $itemKey): ?string
    {
        if (!config('dictionary.cache_enabled')) {
            return DictionaryItem::where('parent_key', $categoryKey)
                ->where('item_key', $itemKey)
                ->value('item_value');
        }

        $cacheKey = config('dictionary.cache_prefix') . "value:{$categoryKey}:{$itemKey}";

        return Cache::remember(
            $cacheKey,
            config('dictionary.cache_ttl'),
            fn() => DictionaryItem::where('parent_key', $categoryKey)
                ->where('item_key', $itemKey)
                ->value('item_value')
        );
    }

    /**
     * 清除字典项缓存
     */
    protected function clearItemCache(string $categoryKey): void
    {
        if (!config('dictionary.cache_enabled')) {
            return;
        }

        $prefix = config('dictionary.cache_prefix');
        Cache::forget($prefix . "items:{$categoryKey}:enabled");
        Cache::forget($prefix . "items:{$categoryKey}:all");

        // 清除所有该分类下的值缓存（使用通配符模式）
        // 注意：这需要缓存驱动支持，如 Redis
        if (method_exists(Cache::getStore(), 'connection')) {
            $pattern = $prefix . "value:{$categoryKey}:*";
            $keys = Cache::getStore()->connection()->keys($pattern);
            foreach ($keys as $key) {
                Cache::forget(str_replace($prefix, '', $key));
            }
        }
    }

    /**
     * 清除树形结构缓存
     */
    protected function clearTreeCache(): void
    {
        if (!config('dictionary.cache_enabled')) {
            return;
        }

        Cache::forget(config('dictionary.cache_prefix') . 'tree');
    }

    /**
     * 刷新所有缓存
     */
    public function refreshCache(?string $categoryKey = null): void
    {
        if (!config('dictionary.cache_enabled')) {
            return;
        }

        if ($categoryKey) {
            $this->clearItemCache($categoryKey);
        } else {
            // 清除所有字典缓存
            $prefix = config('dictionary.cache_prefix');

            if (method_exists(Cache::getStore(), 'connection')) {
                $pattern = $prefix . '*';
                $keys = Cache::getStore()->connection()->keys($pattern);
                foreach ($keys as $key) {
                    Cache::forget(str_replace($prefix, '', $key));
                }
            } else {
                // 如果不支持通配符，至少清除树缓存
                $this->clearTreeCache();
            }
        }
    }
}
