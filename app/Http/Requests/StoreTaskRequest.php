<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // auth:sanctum middleware already ensures user is logged in
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'due_at' => ['nullable', 'date', 'after_or_equal:now'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
