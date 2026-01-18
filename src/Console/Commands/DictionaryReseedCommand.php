<?php

namespace WeiJuKeJi\LaravelDictionary\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use WeiJuKeJi\LaravelDictionary\Database\Seeders\DictionarySeeder;

class DictionaryReseedCommand extends Command
{
    protected $signature = 'dictionary:reseed
                            {--force : 强制执行，不询问确认}';

    protected $description = '清空并重新填充字典数据';

    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('此操作将清空所有字典数据并重新填充，确定继续吗？')) {
                $this->info('操作已取消');
                return self::SUCCESS;
            }
        }

        $this->info('');
        $this->info('  ╔══════════════════════════════════════╗');
        $this->info('  ║       字典数据重置中...             ║');
        $this->info('  ╚══════════════════════════════════════╝');
        $this->info('');

        // 1. 清空字典数据
        $this->clearDictionaryData();

        // 2. 重新填充
        $this->reseedDictionaryData();

        // 3. 清理缓存
        $this->clearCache();

        // 4. 显示完成信息
        $this->showCompletionInfo();

        return self::SUCCESS;
    }

    protected function clearDictionaryData(): void
    {
        $this->info('🗑️  清空现有字典数据...');

        try {
            DB::beginTransaction();

            $tableCategories = config('dictionary.table_categories', 'dictionary_categories');
            $tableItems = config('dictionary.table_items', 'dictionary_items');

            // 清空字典项表
            DB::table($tableItems)->truncate();

            // 清空字典分类表
            DB::table($tableCategories)->truncate();

            DB::commit();

            $this->line('  ✓ 字典数据已清空');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('  ✗ 清空失败: '.$e->getMessage());
            exit(self::FAILURE);
        }
    }

    protected function reseedDictionaryData(): void
    {
        $this->info('🌱 重新填充字典数据...');

        try {
            $seeder = new DictionarySeeder();
            $seeder->setCommand($this);
            $seeder->run();

            $this->line('  ✓ 字典数据已重新填充');
        } catch (\Exception $e) {
            $this->error('  ✗ 填充失败: '.$e->getMessage());
            exit(self::FAILURE);
        }
    }

    protected function clearCache(): void
    {
        $this->info('🧹 清理字典缓存...');

        if (config('dictionary.cache_enabled', true)) {
            $prefix = config('dictionary.cache_prefix', 'dict:');
            $driver = config('dictionary.cache_driver');

            $cache = $driver ? cache()->store($driver) : cache();

            // 清理所有字典相关缓存
            $cache->flush();
        }

        $this->line('  ✓ 缓存已清理');
    }

    protected function showCompletionInfo(): void
    {
        $this->newLine();
        $this->info('══════════════════════════════════════════');
        $this->info('  ✅ 字典数据重置完成!');
        $this->info('══════════════════════════════════════════');
        $this->newLine();

        $tableCategories = config('dictionary.table_categories', 'dictionary_categories');
        $tableItems = config('dictionary.table_items', 'dictionary_items');

        $categoryCount = DB::table($tableCategories)->count();
        $itemCount = DB::table($tableItems)->count();

        $this->line("  <fg=cyan>字典分类总数:</> {$categoryCount}");
        $this->line("  <fg=cyan>字典项总数:</> {$itemCount}");
        $this->newLine();
    }
}
