<?php

namespace WeiJuKeJi\LaravelDictionary\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;

class DictionarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = database_path('seeders/dictionaries.json');

        if (! File::exists($jsonPath)) {
            $this->command?->warn("字典数据文件不存在：{$jsonPath}");
            $this->command?->line('提示：运行 php artisan dictionary:export 导出现有数据');
            return;
        }

        $json = File::get($jsonPath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command?->error('JSON 解析失败：'.json_last_error_msg());
            return;
        }

        DB::beginTransaction();

        try {
            // 1. 导入字典分类
            $this->seedCategories($data['categories'] ?? []);

            // 2. 导入字典项
            $this->seedItems($data['items'] ?? []);

            DB::commit();

            $categoryCount = count($data['categories'] ?? []);
            $itemCount = count($data['items'] ?? []);

            $this->command?->info("✓ 成功导入 {$categoryCount} 个分类，{$itemCount} 个字典项");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command?->error('导入失败：'.$e->getMessage());
            throw $e;
        }
    }

    /**
     * 导入字典分类
     */
    protected function seedCategories(array $categories): void
    {
        if (empty($categories)) {
            return;
        }

        $this->command?->line('  → 导入字典分类...');

        foreach ($categories as $category) {
            DictionaryCategory::create([
                'parent_id' => $category['parent_id'] ?? null,
                'category_key' => $category['category_key'],
                'category_name' => $category['category_name'],
                'sort_order' => $category['sort_order'] ?? 0,
            ]);
        }
    }

    /**
     * 导入字典项
     */
    protected function seedItems(array $items): void
    {
        if (empty($items)) {
            return;
        }

        $this->command?->line('  → 导入字典项...');

        foreach ($items as $item) {
            DictionaryItem::create([
                'parent_key' => $item['parent_key'],
                'item_key' => $item['item_key'],
                'item_value' => $item['item_value'],
                'sort_order' => $item['sort_order'] ?? 0,
                'is_enabled' => $item['is_enabled'] ?? true,
            ]);
        }
    }
}
