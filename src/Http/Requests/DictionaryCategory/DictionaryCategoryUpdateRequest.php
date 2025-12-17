<?php

namespace WeiJuKeJi\LaravelDictionary\Http\Requests\DictionaryCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DictionaryCategoryUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('dictionary_category')->id;

        return [
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:dictionary_categories,id'],
            'category_key' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('dictionary_categories', 'category_key')->ignore($id)
            ],
            'category_name' => ['sometimes', 'required', 'string', 'max:200'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'parent_id' => '父分类ID',
            'category_key' => '分类Key',
            'category_name' => '分类名称',
            'sort_order' => '排序',
        ];
    }

    public function messages(): array
    {
        return [
            'category_key.unique' => '该分类 Key 已存在',
        ];
    }
}
