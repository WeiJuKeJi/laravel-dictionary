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
        Schema::create(config('dictionary.table_categories', 'dictionary_categories'), function (Blueprint $table) {
            $table->id()->comment('分类ID');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父级分类ID');
            $table->string('category_key', 100)->unique()->comment('分类key值');
            $table->string('category_name', 200)->comment('分类名称');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();

            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('dictionary.table_categories', 'dictionary_categories'));
    }
};
