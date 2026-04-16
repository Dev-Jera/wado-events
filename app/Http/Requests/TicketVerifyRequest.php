<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketVerifyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_event_id' => ['required', 'integer', 'exists:events,id'],
            'ticket_code' => ['nullable', 'string'],
            'scanned_payload' => ['nullable', 'string'],
            'lookup' => ['nullable', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:120'],
        ];
    }
}
