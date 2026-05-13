<?php

namespace App\Http\Requests;

use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        return [
            'ticket_category_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'holder_name' => ['required', 'string', 'max:120'],
            'email' => [$user ? 'nullable' : 'required', 'email:rfc,dns', 'max:255'],
            'payment_provider' => ['nullable', 'in:mtn,airtel'],
            'phone_number' => ['nullable', 'regex:/^\+?[1-9]\d{7,14}$/'],
            'create_account' => ['nullable', 'boolean'],
            'password' => ['nullable', 'string', 'min:8', 'max:72', 'required_if:create_account,1', 'confirmed'],
            'idempotency_key' => ['required', 'string', 'max:64'],
            'promo_code' => ['nullable', 'string', 'max:32'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalizedPhone = preg_replace('/(?!^)\+|[^\d+]/', '', trim((string) $this->input('phone_number')));
        if (str_starts_with((string) $normalizedPhone, '00')) {
            $normalizedPhone = '+' . substr((string) $normalizedPhone, 2);
        }

        $normalizedEmail = Str::lower(trim((string) $this->input('email')));
        $normalizedPromo = Str::upper(trim((string) $this->input('promo_code')));

        $this->merge([
            'holder_name' => trim((string) $this->input('holder_name')),
            'email' => blank($normalizedEmail) ? null : $normalizedEmail,
            'phone_number' => blank($normalizedPhone) ? null : $normalizedPhone,
            'promo_code' => blank($normalizedPromo) ? null : $normalizedPromo,
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $event = $this->route('event');
            if (! $event instanceof Event) {
                return;
            }

            $categoryId = (int) $this->input('ticket_category_id');
            $category = TicketCategory::query()
                ->where('event_id', $event->id)
                ->find($categoryId);

            if (! $category) {
                $validator->errors()->add('ticket_category_id', 'The selected ticket category is invalid for this event.');

                return;
            }

            if ((float) $category->price <= 0) {
                return;
            }

            if (blank($this->input('payment_provider'))) {
                $validator->errors()->add('payment_provider', 'Choose MTN or Airtel to continue.');
            }

            if (blank($this->input('phone_number'))) {
                $validator->errors()->add('phone_number', 'Phone number is required for mobile money payments.');
            }
        });
    }
}
