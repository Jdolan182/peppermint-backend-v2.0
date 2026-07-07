<?php

namespace App\Http\Requests\Admin\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $this->route('user')->id],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'      => ['sometimes', 'boolean'],
            'notify_contact' => ['sometimes', 'boolean'],
        ];
    }
}
