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
        $query = DictionaryCategory::query();

        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->input('parent_id'));
        }

        $categories = $query->filter($request->all())
            ->orderBy('sort_order')
            ->paginate($request->input('per_page', 15));

        return $this->success([
            'list' => DictionaryCategoryResource::collection($categories->items()),
            'total' => $categories->total(),
            'current_page' => $categories->currentPage(),
            'per_page' => $categories->perPage(),
        ], '获取成功');
    }

    /**
     * 创建字典分类
     */
    public function storeCategory(DictionaryCategoryStoreRequest $request, DictionaryService $dictionaryService): JsonResponse
    {
        $category = $dictionaryService->saveCategory($request->validated());

        return $this->success(
            DictionaryCategoryResource::make($category),
            '创建成功',
            201
        );
    }

    /**
     * 更新字典分类
     */
    public function updateCategory(DictionaryCategoryUpdateRequest $request, DictionaryCategory $dictionaryCategory, DictionaryService $dictionaryService): JsonResponse
    {
        $data = array_merge($request->validated(), ['id' => $dictionaryCategory->id]);
        $category = $dictionaryService->saveCategory($data);

        return $this->success(
            DictionaryCategoryResource::make($category),
            '更新成功'
        );
    }

    /**
     * 删除字典分类
     */
    public function destroyCategory(DictionaryCategory $dictionaryCategory, DictionaryService $dictionaryService): JsonResponse
    {
        $dictionaryService->deleteCategory($dictionaryCategory->id);

        return $this->success(null, '删除成功');
    }

    /**
     * 获取字典项列表
     */
    public function items(Request $request): JsonResponse
    {
        $query = DictionaryItem::query();

        if ($request->has('parent_key')) {
            $query->where('parent_key', $request->input('parent_key'));
        }

        $items = $query->filter($request->all())
            ->orderBy('sort_order')
            ->paginate($request->input('per_page', 15));

        return $this->success([
            'list' => DictionaryItemResource::collection($items->items()),
            'total' => $items->total(),
            'current_page' => $items->currentPage(),
            'per_page' => $items->perPage(),
        ], '获取成功');
    }

    /**
     * 创建字典项
     */
    public function storeItem(DictionaryItemStoreRequest $request, DictionaryService $dictionaryService): JsonResponse
    {
        $item = $dictionaryService->saveItem($request->validated());

        return $this->success(
            DictionaryItemResource::make($item),
            '创建成功',
            201
        );
    }

    /**
     * 更新字典项
     */
    public function updateItem(DictionaryItemUpdateRequest $request, DictionaryItem $dictionaryItem, DictionaryService $dictionaryService): JsonResponse
    {
        $data = array_merge($request->validated(), ['id' => $dictionaryItem->id]);
        $item = $dictionaryService->saveItem($data);

        return $this->success(
            DictionaryItemResource::make($item),
            '更新成功'
        );
    }

    /**
     * 删除字典项
     */
    public function destroyItem(DictionaryItem $dictionaryItem, DictionaryService $dictionaryService): JsonResponse
    {
        $dictionaryService->deleteItem($dictionaryItem->id);

        return $this->success(null, '删除成功');
    }

    /**
     * 根据分类键获取启用的字典项列表
     */
    public function getItemsByKey(string $categoryKey): JsonResponse
    {
        if ($categoryKey === 'root') {
            return $this->success(['list' => []], '获取成功');
        }

        $items = DictionaryItem::where('parent_key', $categoryKey)
            ->enabled()
            ->orderBy('sort_order')
            ->get();

        return $this->success([
            'list' => DictionaryItemResource::collection($items),
        ], '获取成功');
    }
}
