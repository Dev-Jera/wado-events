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
            'ticket_code' => ['nullable', 'string', 'max:120'],
            'scanned_payload' => ['nullable', 'string', 'max:2000'],
            'lookup' => ['nullable', 'string', 'max:255'],
            'device_id' => ['nullable', 'string', 'max:120'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizedTicketCode = trim((string) $this->input('ticket_code'));
        $normalizedPayload = trim((string) $this->input('scanned_payload'));
        $normalizedLookup = trim((string) $this->input('lookup'));
        $normalizedDeviceId = trim((string) $this->input('device_id'));

        $this->merge([
            'ticket_code' => blank($normalizedTicketCode) ? null : $normalizedTicketCode,
            'scanned_payload' => blank($normalizedPayload) ? null : $normalizedPayload,
            'lookup' => blank($normalizedLookup) ? null : $normalizedLookup,
            'device_id' => blank($normalizedDeviceId) ? null : $normalizedDeviceId,
        ]);
    }
}
