<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'email' => ['nullable', 'email', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:40'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'payment_channel' => ['required', 'in:mtn,airtel,cash,pos'],
            'collector_reference' => ['nullable', 'string', 'max:120'],
        ];
    }
}
