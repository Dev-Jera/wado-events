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
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['required', 'in:draft,published,cancelled'],
            'image_file' => ['nullable', 'image', 'max:5120'],
            'is_free' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'artists' => ['nullable', 'array'],
            'artists.*.name' => ['nullable', 'string', 'max:120'],
            'ticket_categories' => ['required', 'array', 'min:1'],
            'ticket_categories.*.name' => ['required', 'string', 'max:100'],
            'ticket_categories.*.price' => ['required', 'numeric', 'min:0', 'max:1000000000'],
            'ticket_categories.*.ticket_count' => ['required', 'integer', 'min:1', 'max:100000'],
            'ticket_categories.*.description' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $rawArtists = $this->input('artists', []);
        $normalizedArtists = collect(is_array($rawArtists) ? $rawArtists : [])
            ->map(function ($artist) {
                $artist = is_array($artist) ? $artist : [];

                return [
                    'name' => trim((string) ($artist['name'] ?? '')),
                ];
            })
            ->values()
            ->all();

        $rawTicketCategories = $this->input('ticket_categories', []);
        $normalizedTicketCategories = collect(is_array($rawTicketCategories) ? $rawTicketCategories : [])
            ->map(function ($ticketCategory) {
                $ticketCategory = is_array($ticketCategory) ? $ticketCategory : [];

                return [
                    'name' => trim((string) ($ticketCategory['name'] ?? '')),
                    'price' => $ticketCategory['price'] ?? null,
                    'ticket_count' => $ticketCategory['ticket_count'] ?? null,
                    'description' => blank(trim((string) ($ticketCategory['description'] ?? '')))
                        ? null
                        : trim((string) ($ticketCategory['description'] ?? '')),
                ];
            })
            ->values()
            ->all();

        $this->merge([
            'title' => trim((string) $this->input('title')),
            'venue' => trim((string) $this->input('venue')),
            'city' => trim((string) $this->input('city')),
            'country' => trim((string) $this->input('country')),
            'description' => trim((string) $this->input('description')),
            'is_free' => $this->boolean('is_free'),
            'is_featured' => $this->boolean('is_featured'),
            'artists' => $normalizedArtists,
            'ticket_categories' => $normalizedTicketCategories,
        ]);
    }
}
