<?php

namespace App\Http\Requests;

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
            'name' => ['required', 'string', 'max:100', 'regex:/^[\pL\pN\s\-\&\'\.]+$/u', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizedDescription = trim((string) $this->input('description'));

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'description' => blank($normalizedDescription) ? null : $normalizedDescription,
        ]);
    }
}
