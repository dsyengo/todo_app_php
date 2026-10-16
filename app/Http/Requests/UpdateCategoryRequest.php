<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'sometimes',
                'string',
                'min:1',
                'max:50',
                Rule::unique('categories')
                    ->where('user_id', $this->user()->id)
                    ->ignore($this->route('category')->id),
            ],
        ];
    }
}
