<?php

namespace App\Http\Requests\Admin\Consumer;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsumerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:consumers,email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
