# Laravel Dictionary

[![Latest Version on Packagist](https://img.shields.io/packagist/v/weijukeji/laravel-dictionary.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-dictionary)
[![Total Downloads](https://img.shields.io/packagist/dt/weijukeji/laravel-dictionary.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-dictionary)
[![License](https://img.shields.io/packagist/l/weijukeji/laravel-dictionary.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-dictionary)

一个功能完整、易于使用的 Laravel 字典管理包，支持分类和字典项的统一管理，内置缓存优化，提供 RESTful API 接口。

[English](README.md) | [简体中文](README_CN.md)

## 特性

- ✨ **分类管理** - 支持字典分类的树形结构管理
- 📝 **字典项管理** - 灵活的字典项增删改查
- 🔢 **自动生成值** - 支持自动生成字典项数值
- ⚡ **高性能** - 内置缓存支持，优化查询性能
- 🔌 **即插即用** - 开箱即用的 API 接口
- 🎯 **RESTful 风格** - 标准的 RESTful API 设计
- 📦 **易于扩展** - 清晰的代码结构，方便二次开发
- 🔒 **数据过滤** - 集成 EloquentFilter 支持复杂查询
- 🌱 **数据导入导出** - 支持导出和导入种子数据
- 🗄️ **多数据库支持** - 支持 MySQL、PostgreSQL、SQLite 等

## 环境要求

- PHP >= 8.2
- Laravel 11.x, 12.x, 13.x

## 安装

通过 Composer 安装包：

```bash
composer require weijukeji/laravel-dictionary
```

发布配置文件和迁移文件：

```bash
# 发布配置文件
php artisan vendor:publish --tag=dictionary-config

# 发布迁移文件
php artisan vendor:publish --tag=dictionary-migrations
```

运行数据库迁移：

```bash
php artisan migrate
```

## 配置

配置文件位于 `config/dictionary.php`：

```php
return [
    // 数据库表名
    'table_categories' => 'dictionary_categories',
    'table_items' => 'dictionary_items',

    // API 路由配置
    'route_prefix' => 'api/dictionaries',
    'route_middleware' => ['api'],

    // 缓存配置
    'cache_enabled' => true,
    'cache_ttl' => 3600,
    'cache_prefix' => 'dict:',
    'cache_driver' => null,

    // 分页配置
    'per_page' => 15,
    'max_per_page' => 100,
];
```

## 使用指南

### API 端点

包提供了完整的 RESTful API 接口：

#### 字典分类

```http
# 获取分类列表（分页）
GET /api/dictionaries/categories

# 获取分类树
GET /api/dictionaries/tree

# 创建分类
POST /api/dictionaries/categories

# 更新分类
PUT /api/dictionaries/categories/{id}

# 删除分类
DELETE /api/dictionaries/categories/{id}
```

#### 字典项

```http
# 获取字典项列表（分页）
GET /api/dictionaries/items

# 根据分类键获取字典项
GET /api/dictionaries/items/by-key/{categoryKey}

# 创建字典项
POST /api/dictionaries/items

# 更新字典项
PUT /api/dictionaries/items/{id}

# 删除字典项
DELETE /api/dictionaries/items/{id}
```

### 请求示例

#### 创建字典分类

```bash
curl -X POST http://your-app.test/api/dictionaries/categories \
  -H "Content-Type: application/json" \
  -d '{
    "parent_id": null,
    "category_key": "status",
    "category_name": "状态分类",
    "sort_order": 1
  }'
```

#### 创建字典项（手动输入键）

```bash
curl -X POST http://your-app.test/api/dictionaries/items \
  -H "Content-Type: application/json" \
  -d '{
    "parent_key": "status",
    "item_key": "1",
    "item_value": "启用",
    "sort_order": 1,
    "is_enabled": true,
    "auto_generate_key": false
  }'
```

#### 创建字典项（自动生成键）

```bash
curl -X POST http://your-app.test/api/dictionaries/items \
  -H "Content-Type: application/json" \
  -d '{
    "parent_key": "status",
    "item_value": "停用",
    "sort_order": 2,
    "is_enabled": true,
    "auto_generate_key": true
  }'
```

#### 获取字典项

```bash
curl http://your-app.test/api/dictionaries/items/by-key/status
```

### 自动生成字典键

创建字典项时，可以选择自动生成数字键：

```php
// 前端传参
{
    "parent_key": "status",
    "item_value": "待处理",
    "auto_generate_key": true  // 自动生成键
}

// 后端自动生成规则：
// 1. 查找该分类下最大的纯数字键
// 2. 在最大值基础上 +1
// 3. 如果没有数字键，从 "1" 开始
```

**生成示例：**
- 首次创建：生成 `"1"`
- 已有 `"1", "2", "3"`：生成 `"4"`
- 已有 `"1", "active", "99"`：生成 `"100"`（忽略非数字键）

### 在代码中使用

#### 使用 Facade

```php
use WeiJuKeJi\LaravelDictionary\Facades\Dict;

// 获取字典项
$items = Dict::getItemsByKey('status');

// 获取分类树
$tree = Dict::getTree();
```

#### 使用模型

```php
use WeiJuKeJi\LaravelDictionary\Models\DictionaryCategory;
use WeiJuKeJi\LaravelDictionary\Models\DictionaryItem;

// 查询分类
$category = DictionaryCategory::where('category_key', 'status')->first();

// 查询字典项
$items = DictionaryItem::where('parent_key', 'status')
    ->enabled()
    ->orderBy('sort_order')
    ->get();
```

#### 使用服务类

```php
use WeiJuKeJi\LaravelDictionary\Services\DictionaryService;

$service = app(DictionaryService::class);

// 获取树形结构
$tree = $service->getTree();

// 保存分类
$category = $service->saveCategory([
    'category_key' => 'status',
    'category_name' => '状态分类',
    'sort_order' => 1
]);

// 保存字典项
$item = $service->saveItem([
    'parent_key' => 'status',
    'item_key' => '1',
    'item_value' => '启用',
    'sort_order' => 1,
    'is_enabled' => true
]);
```

## 数据导入导出

### 导出字典数据

将当前数据库中的字典数据导出为 JSON 文件：

```bash
# 导出到默认路径 database/seeders/dictionaries.json
php artisan dictionary:export

# 导出到指定路径
php artisan dictionary:export storage/app/dictionaries.json
```

### 导入字典数据

从 JSON 文件导入字典数据：

```bash
# 使用 Seeder 导入
php artisan db:seed --class=WeiJuKeJi\\LaravelDictionary\\Database\\Seeders\\DictionarySeeder

# 清空并重新导入（交互式确认）
php artisan dictionary:reseed

# 强制重新导入（不询问）
php artisan dictionary:reseed --force
```

### 在 DatabaseSeeder 中使用

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use WeiJuKeJi\LaravelDictionary\Database\Seeders\DictionarySeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            DictionarySeeder::class,
            // 其他 Seeders...
        ]);
    }
}
```

### JSON 文件格式

```json
{
  "categories": [
    {
      "parent_id": null,
      "category_key": "status",
      "category_name": "状态分类",
      "sort_order": 1
    }
  ],
  "items": [
    {
      "parent_key": "status",
      "item_key": "1",
      "item_value": "启用",
      "sort_order": 1,
      "is_enabled": true
    }
  ]
}
```

## 数据结构

### 字典分类表 (dictionary_categories)

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| parent_id | bigint | 父分类ID |
| category_key | string | 分类键（唯一） |
| category_name | string | 分类名称 |
| sort_order | integer | 排序 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

### 字典项表 (dictionary_items)

| 字段 | 类型 | 说明 |
|------|------|------|
| id | bigint | 主键 |
| parent_key | string | 所属分类键 |
| item_key | string | 字典项键 |
| item_value | string | 字典项值 |
| sort_order | integer | 排序 |
| is_enabled | boolean | 是否启用 |
| created_at | timestamp | 创建时间 |
| updated_at | timestamp | 更新时间 |

## 响应格式

所有 API 接口返回统一的 JSON 格式：

### 成功响应

```json
{
  "code": 200,
  "msg": "success",
  "data": {
    "list": [...],
    "total": 100
  }
}
```

### 错误响应

```json
{
  "code": 400,
  "msg": "错误信息",
  "data": {
    "errors": []
  }
}
```

## 数据保护

### 删除保护

- ❌ 不能删除有子分类的分类
- ❌ 不能删除有字典项的分类
- ✅ 必须先清空数据才能删除

### 唯一性约束

- 分类键（category_key）全局唯一
- 字典项键（item_key）在同一分类下唯一

## 命令行工具

| 命令 | 说明 |
|------|------|
| `dictionary:export [path]` | 导出字典数据到 JSON 文件 |
| `dictionary:reseed [--force]` | 清空并重新导入字典数据 |

## 测试

```bash
composer test
```

## 更新日志

请查看 [CHANGELOG](CHANGELOG.md) 了解更多信息。

## 贡献

欢迎贡献代码！请查看 [CONTRIBUTING](CONTRIBUTING.md) 了解详情。

## 安全

如果发现任何安全相关问题，请发送邮件至 dev@weijukeji.com 而不是使用 issue 跟踪器。

## 许可证

MIT 许可证。详情请查看 [LICENSE](LICENSE) 文件。

## 致谢

- 基于 [Laravel](https://laravel.com/) 框架开发
- 使用 [EloquentFilter](https://github.com/Tucker-Eric/EloquentFilter) 进行数据过滤
