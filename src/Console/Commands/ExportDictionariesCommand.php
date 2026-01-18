<?php

namespace WeiJuKeJi\LaravelDictionary\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Throwable;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;

class ExportDictionariesCommand extends Command
{
    protected $signature = 'dictionary:export {path? : 输出 JSON 文件路径，默认 database/seeders/dictionaries.json}';

    protected $description = '将字典分类和字典项导出为 JSON 文件，供 DictionarySeeder 使用';

    public function handle(): int
    {
        $outputPath = $this->argument('path') ?? database_path('seeders/dictionaries.json');

        $categories = DictionaryCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($categories->isEmpty()) {
            $this->warn('字典分类表暂无数据，未生成任何文件。');
            return self::SUCCESS;
        }

        $payload = [
            'categories' => $categories->map(fn (DictionaryCategory $category) => $this->transformCategory($category))->values()->all(),
            'items' => $this->exportItems(),
        ];

        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

        if ($json === false) {
            $this->error('JSON 序列化失败：'.json_last_error_msg());
            return self::FAILURE;
        }

        try {
            File::ensureDirectoryExists(dirname($outputPath));
            File::put($outputPath, $json.PHP_EOL);
        } catch (Throwable $exception) {
            $this->error('写入文件失败：'.$exception->getMessage());
            return self::FAILURE;
        }

        $this->info('字典数据 JSON 导出完成。');
        $this->line('输出路径：'.$outputPath);
        $this->newLine();
        $this->line('提示：下次运行 db:seed 时，DictionarySeeder 会自动读取此文件。');

        return self::SUCCESS;
    }

    protected function transformCategory(DictionaryCategory $category): array
    {
        $data = [
            'parent_id' => $category->parent_id,
            'category_key' => $category->category_key,
            'category_name' => $category->category_name,
            'sort_order' => $category->sort_order,
        ];

        return $data;
    }

    protected function exportItems(): array
    {
        $items = DictionaryItem::query()
            ->orderBy('parent_key')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $items->map(function (DictionaryItem $item) {
            return [
                'parent_key' => $item->parent_key,
                'item_key' => $item->item_key,
                'item_value' => $item->item_value,
                'sort_order' => $item->sort_order,
                'is_enabled' => (bool) $item->is_enabled,
            ];
        })->all();
    }
}
