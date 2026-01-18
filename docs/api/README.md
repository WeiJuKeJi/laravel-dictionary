# Laravel Dictionary API 文档索引

本目录包含 Laravel Dictionary 字典管理包的所有 API 接口文档（OpenAPI 3.0.3 格式）。

## 如何使用

1. 使用 Apifox 导入对应的 JSON 文件
2. 根据下表快速定位控制器代码位置
3. 参考控制器方法表了解接口实现细节

---

## API 文档列表

| API 文档 | 对应控制器 | 说明 |
|---------|-----------|------|
| [字典管理.json](./字典管理.json) | DictionaryController | 字典分类和字典项管理相关接口 |

---

## 控制器详情

### DictionaryController

**文件路径**: `src/Http/Controllers/DictionaryController.php`
**API 文档**: [字典管理.json](./字典管理.json)

#### 字典分类接口

| 控制器方法 | HTTP 方法 | 路由 | 接口说明 | operationId |
|-----------|----------|------|---------|-------------|
| getTree() | GET | /tree | 获取字典分类树 | getTree |
| categories() | GET | /categories | 获取字典分类列表 | getCategories |
| storeCategory() | POST | /categories | 创建字典分类 | createCategory |
| updateCategory() | PUT | /categories/{id} | 更新字典分类 | updateCategory |
| destroyCategory() | DELETE | /categories/{id} | 删除字典分类 | deleteCategory |

#### 字典项接口

| 控制器方法 | HTTP 方法 | 路由 | 接口说明 | operationId |
|-----------|----------|------|---------|-------------|
| items() | GET | /items | 获取字典项列表 | getItems |
| getItemsByKey() | GET | /items/by-key/{categoryKey} | 根据分类键获取字典项 | getItemsByKey |
| storeItem() | POST | /items | 创建字典项 | createItem |
| updateItem() | PUT | /items/{id} | 更新字典项 | updateItem |
| destroyItem() | DELETE | /items/{id} | 删除字典项 | deleteItem |

---

## 请求示例

### 基础配置

所有接口都需要包含以下 Header：

```bash
Accept: application/json
Authorization: Bearer {your_token}
```

### 完整路径

根据配置文件 `config/dictionary.php` 中的 `api.prefix` 设置，默认的完整路径为：

```
http://your-domain.com/api/dictionaries/{endpoint}
```

例如：
- 获取分类树：`GET /api/dictionaries/tree`
- 获取字典项列表：`GET /api/dictionaries/items`
- 根据分类键获取字典项：`GET /api/dictionaries/items/by-key/status`

### 响应格式

所有接口统一使用以下响应格式：

```json
{
  "code": 200,
  "msg": "success",
  "data": {
    // 实际数据
  }
}
```

错误响应：

```json
{
  "code": 422,
  "msg": "数据校验失败",
  "data": {
    "errors": {
      "field_name": ["错误信息"]
    }
  }
}
```

---

## 数据模型

### DictionaryCategory（字典分类）

| 字段 | 类型 | 必填 | 说明 |
|-----|------|------|------|
| id | integer | - | 分类ID（自动生成） |
| parent_id | integer/null | 否 | 父分类ID |
| category_key | string(100) | 是 | 分类键（唯一） |
| category_name | string(200) | 是 | 分类名称 |
| sort_order | integer | 否 | 排序值（默认0） |
| created_at | datetime | - | 创建时间 |
| updated_at | datetime | - | 更新时间 |

### DictionaryItem（字典项）

| 字段 | 类型 | 必填 | 说明 |
|-----|------|------|------|
| id | integer | - | 字典项ID（自动生成） |
| parent_key | string(100) | 是 | 所属分类键 |
| item_key | string(100) | 是 | 字典项键（在同一分类下唯一） |
| item_value | string(200) | 是 | 字典项值 |
| sort_order | integer | 否 | 排序值（默认0） |
| is_enabled | boolean | 否 | 是否启用（默认true） |
| is_enabled_text | string | - | 启用状态文本（自动生成） |
| created_at | datetime | - | 创建时间 |
| updated_at | datetime | - | 更新时间 |

---

## 版本历史

| 版本 | 日期 | 说明 |
|-----|------|------|
| 1.0.0 | 2025-01-17 | 初始版本，包含字典分类和字典项的完整 CRUD 接口 |
