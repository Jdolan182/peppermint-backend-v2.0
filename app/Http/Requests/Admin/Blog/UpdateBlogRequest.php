<?php

namespace App\Http\Requests\Admin\Blog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'content'        => ['required', 'string'],
            'excerpt'        => ['nullable', 'string', 'max:500'],
            'published_at'   => ['nullable', 'date'],
            'category_ids'   => ['nullable', 'array'],
            'category_ids.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
