<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(config('dictionary.table_items', 'dictionary_items'), function (Blueprint $table) {
            $table->id()->comment('字典项ID');
            $table->string('parent_key', 100)->comment('父级分类key值');
            $table->string('item_key', 100)->comment('字典项key值');
            $table->string('item_value', 200)->comment('字典项显示值');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_enabled')->default(true)->comment('是否启用');
            $table->timestamps();

            $table->index('parent_key');
            $table->unique(['parent_key', 'item_key'], 'uk_parent_item_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('dictionary.table_items', 'dictionary_items'));
    }
};
