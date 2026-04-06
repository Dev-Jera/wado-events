<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'venue' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'country' => ['required', 'string', 'max:120'],
            'description' => ['required', 'string', 'min:20'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', 'in:draft,published,cancelled'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'is_featured' => ['nullable', 'boolean'],
            'ticket_categories' => ['required', 'array', 'min:1'],
            'ticket_categories.*.name' => ['required', 'string', 'max:100'],
            'ticket_categories.*.price' => ['required', 'numeric', 'min:0'],
            'ticket_categories.*.ticket_count' => ['required', 'integer', 'min:1'],
            'ticket_categories.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
        ]);
    }
}
