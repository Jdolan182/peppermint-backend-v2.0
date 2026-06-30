<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:100', 'unique:categories,name'],
            'color' => ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ];
    }
}
