<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Requests\DictionaryItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DictionaryItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_key' => ['required', 'string', 'max:100'],
            'item_key' => [
                'required_if:auto_generate_key,false',
                'nullable',
                'string',
                'max:100',
                Rule::unique('dictionary_items', 'item_key')
                    ->where('parent_key', $this->input('parent_key'))
            ],
            'item_value' => ['required', 'string', 'max:200'],
            'auto_generate_key' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_enabled' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_key' => '父分类Key',
            'item_key' => '字典项Key',
            'item_value' => '字典项值',
            'auto_generate_key' => '自动生成字典Key',
            'sort_order' => '排序',
            'is_enabled' => '是否启用',
        ];
    }

    public function messages(): array
    {
        return [
            'item_key.unique' => '该分类下已存在相同的 Key 值',
        ];
    }

    protected function prepareForValidation()
    {
        if ($this->has('is_enabled')) {
            $this->merge(['is_enabled' => $this->boolean('is_enabled')]);
        }

        if ($this->has('auto_generate_key')) {
            $this->merge(['auto_generate_key' => $this->boolean('auto_generate_key')]);
        }
    }
}
