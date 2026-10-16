<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:1', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'priority' => ['sometimes', 'string', 'in:low,medium,high'],
            'due_at' => ['nullable', 'date'],
            'is_completed' => ['sometimes', 'boolean'],
            'categories' => ['sometimes', 'array'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
}
