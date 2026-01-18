<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;
use WeiJuKeJi\LaravelDictionary\Services\DictionaryService;
use WeiJuKeJi\LaravelDictionary\Http\Requests\DictionaryItem\DictionaryItemStoreRequest;
use WeiJuKeJi\LaravelDictionary\Http\Requests\DictionaryItem\DictionaryItemUpdateRequest;
use WeiJuKeJi\LaravelDictionary\Http\Requests\DictionaryCategory\DictionaryCategoryStoreRequest;
use WeiJuKeJi\LaravelDictionary\Http\Requests\DictionaryCategory\DictionaryCategoryUpdateRequest;
use WeiJuKeJi\LaravelDictionary\Http\Resources\TreeNodeResource;
use WeiJuKeJi\LaravelDictionary\Http\Resources\DictionaryItemResource;
use WeiJuKeJi\LaravelDictionary\Http\Resources\DictionaryCategoryResource;

/**
 * 字典管理控制器
 */
class DictionaryController extends Controller
{
    /**
     * 获取字典分类树
     */
    public function getTree(DictionaryService $dictionaryService): JsonResponse
    {
        $tree = $dictionaryService->getTree();

        return $this->success([
            'list' => TreeNodeResource::collection($tree),
        ], '获取成功');
    }

    /**
     * 获取字典分类列表
     */
    public function categories(Request $request): JsonResponse
    {
        $params = $request->all();
        $perPage = $this->resolvePerPage($params);

        $query = DictionaryCategory::query();

        if (isset($params['parent_id'])) {
            $query->where('parent_id', $params['parent_id']);
        }

        $categories = $query->filter($params)
            ->orderBy('sort_order')
            ->paginate($perPage);

        return $this->respondWithPagination($categories, DictionaryCategoryResource::class, '获取成功');
    }

    /**
     * 创建字典分类
     */
    public function storeCategory(DictionaryCategoryStoreRequest $request, DictionaryService $dictionaryService): JsonResponse
    {
        $category = $dictionaryService->saveCategory($request->validated());

        return $this->respondWithResource($category, DictionaryCategoryResource::class, '创建成功');
    }

    /**
     * 更新字典分类
     */
    public function updateCategory(DictionaryCategoryUpdateRequest $request, DictionaryCategory $dictionaryCategory, DictionaryService $dictionaryService): JsonResponse
    {
        $data = array_merge($request->validated(), ['id' => $dictionaryCategory->id]);
        $category = $dictionaryService->saveCategory($data);

        return $this->respondWithResource($category, DictionaryCategoryResource::class, '更新成功');
    }

    /**
     * 删除字典分类
     */
    public function destroyCategory(DictionaryCategory $dictionaryCategory, DictionaryService $dictionaryService): JsonResponse
    {
        try {
            $dictionaryService->deleteCategory($dictionaryCategory->id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * 获取字典项列表
     */
    public function items(Request $request): JsonResponse
    {
        $params = $request->all();
        $perPage = $this->resolvePerPage($params);

        $query = DictionaryItem::query();

        if (isset($params['parent_key'])) {
            $query->where('parent_key', $params['parent_key']);
        }

        $items = $query->filter($params)
            ->orderBy('sort_order')
            ->paginate($perPage);

        return $this->respondWithPagination($items, DictionaryItemResource::class, '获取成功');
    }

    /**
     * 创建字典项
     */
    public function storeItem(DictionaryItemStoreRequest $request, DictionaryService $dictionaryService): JsonResponse
    {
        $item = $dictionaryService->saveItem($request->validated());

        return $this->respondWithResource($item, DictionaryItemResource::class, '创建成功');
    }

    /**
     * 更新字典项
     */
    public function updateItem(DictionaryItemUpdateRequest $request, DictionaryItem $dictionaryItem, DictionaryService $dictionaryService): JsonResponse
    {
        $data = array_merge($request->validated(), ['id' => $dictionaryItem->id]);
        $item = $dictionaryService->saveItem($data);

        return $this->respondWithResource($item, DictionaryItemResource::class, '更新成功');
    }

    /**
     * 删除字典项
     */
    public function destroyItem(DictionaryItem $dictionaryItem, DictionaryService $dictionaryService): JsonResponse
    {
        try {
            $dictionaryService->deleteItem($dictionaryItem->id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    /**
     * 根据分类键获取启用的字典项列表
     */
    public function getItemsByKey(string $categoryKey, DictionaryService $dictionaryService): JsonResponse
    {
        if ($categoryKey === 'root') {
            return $this->respondWithList([], 0, '获取成功');
        }

        $items = $dictionaryService->getItemsByKey($categoryKey, true);

        $list = DictionaryItemResource::collection($items)->toArray(request());

        return $this->respondWithList($list, count($list), '获取成功');
    }
}
