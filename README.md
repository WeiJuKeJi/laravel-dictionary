# Laravel Dictionary

[![Latest Version on Packagist](https://img.shields.io/packagist/v/weijukeji/laravel-dictionary.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-dictionary)
[![Total Downloads](https://img.shields.io/packagist/dt/weijukeji/laravel-dictionary.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-dictionary)
[![License](https://img.shields.io/packagist/l/weijukeji/laravel-dictionary.svg?style=flat-square)](https://packagist.org/packages/weijukeji/laravel-dictionary)

一个功能完整、易于使用的 Laravel 字典管理包，支持分类和字典项的统一管理，内置缓存优化，提供 RESTful API 接口。

[English](README.md) | [简体中文](README_CN.md)

## 特性

- ✨ **分类管理** - 支持字典分类的树形结构管理
- 📝 **字典项管理** - 灵活的字典项增删改查
- ⚡ **高性能** - 内置缓存支持，优化查询性能
- 🔌 **即插即用** - 开箱即用的 API 接口
- 🎯 **RESTful 风格** - 标准的 RESTful API 设计
- 📦 **易于扩展** - 清晰的代码结构，方便二次开发
- 🔒 **数据过滤** - 集成 EloquentFilter 支持复杂查询

## 环境要求

- PHP >= 8.2
- Laravel >= 11.0

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

    // 缓存配置
    'cache' => [
        'enabled' => true,
        'ttl' => 3600,
        'prefix' => 'dict:',
        'driver' => null,
    ],

    // API 路由配置
    'api' => [
        'enabled' => true,
        'prefix' => 'api/dictionaries',
        'middleware' => ['api'],
    ],
];
```

## 使用指南

### API 端点

包提供了完整的 RESTful API 接口：

#### 字典分类

```http
# 获取分类列表
GET /api/dictionaries/categories

# 创建分类
POST /api/dictionaries/categories

# 更新分类
PUT /api/dictionaries/categories/{id}

# 删除分类
DELETE /api/dictionaries/categories/{id}

# 获取分类树
GET /api/dictionaries/tree
```

#### 字典项

```http
# 获取字典项列表
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

#### 创建字典项

```bash
curl -X POST http://your-app.test/api/dictionaries/items \
  -H "Content-Type: application/json" \
  -d '{
    "parent_key": "status",
    "item_key": "active",
    "item_value": "激活",
    "sort_order": 1,
    "is_enabled": true
  }'
```

#### 获取字典项

```bash
curl http://your-app.test/api/dictionaries/items/by-key/status
```

### 在代码中使用

#### 使用 Facade（如果实现了 Facade）

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
    'item_key' => 'active',
    'item_value' => '激活',
    'sort_order' => 1,
    'is_enabled' => true
]);
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
