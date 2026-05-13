<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class WalkInSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'ticket_category_id' => ['required', 'integer', 'exists:ticket_categories,id'],
            'holder_name' => ['required', 'string', 'max:120'],
            'email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'phone_number' => ['nullable', 'regex:/^\+?[1-9]\d{7,14}$/'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_channel' => ['required', 'in:mtn,airtel,cash,pos'],
            'collector_reference' => ['nullable', 'string', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizedPhone = preg_replace('/(?!^)\+|[^\d+]/', '', trim((string) $this->input('phone_number')));
        if (str_starts_with((string) $normalizedPhone, '00')) {
            $normalizedPhone = '+' . substr((string) $normalizedPhone, 2);
        }

        $normalizedEmail = Str::lower(trim((string) $this->input('email')));
        $normalizedReference = trim((string) $this->input('collector_reference'));

        $this->merge([
            'holder_name' => trim((string) $this->input('holder_name')),
            'email' => blank($normalizedEmail) ? null : $normalizedEmail,
            'phone_number' => blank($normalizedPhone) ? null : $normalizedPhone,
            'collector_reference' => blank($normalizedReference) ? null : $normalizedReference,
        ]);
    }
}
