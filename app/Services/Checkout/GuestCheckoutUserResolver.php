<?php

namespace App\Services\Checkout;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GuestCheckoutUserResolver
{
    /**
     * Resolve the customer used for checkout.
     * Guest checkout creates a customer record without forcing sign-in.
     * Optional account creation signs in the user immediately.
     *
     * @return array{user: User, guest_checkout: bool}
     */
    public function resolve(Request $request, array $data, bool $createAccount): array
    {
        if ($request->user()) {
            return [
                'user' => $request->user(),
                'guest_checkout' => false,
            ];
        }

        $email = strtolower(trim((string) ($data['email'] ?? '')));
        $holderName = trim((string) ($data['holder_name'] ?? ''));
        $phone = trim((string) ($data['phone_number'] ?? '')) ?: null;
        $sessionGuestUserId = (int) $request->session()->get('checkout_guest_user_id', 0);

        $existing = User::query()->where('email', $email)->first();
        if ($existing) {
            if ($sessionGuestUserId > 0 && $existing->id === $sessionGuestUserId) {
                $updates = [];
                if ($holderName !== '' && blank($existing->name)) {
                    $updates['name'] = $holderName;
                }
                if (! blank($phone) && blank($existing->phone)) {
                    $updates['phone'] = $phone;
                }

                if ($updates !== []) {
                    $existing->forceFill($updates)->save();
                }

                if ($createAccount) {
                    $existing->forceFill([
                        'password' => (string) $data['password'],
                    ])->save();

                    Auth::login($existing);
                    $request->session()->regenerate();
                    $request->session()->forget('checkout_guest_user_id');

                    return [
                        'user' => $existing,
                        'guest_checkout' => false,
                    ];
                }

                return [
                    'user' => $existing,
                    'guest_checkout' => true,
                ];
            }

            throw ValidationException::withMessages([
                'email' => 'An account with this email already exists. Log in to track tickets or use a different email for guest checkout.',
            ]);
        }

        $user = User::query()->create([
            'name' => $holderName,
            'email' => $email,
            'phone' => $phone,
            'password' => $createAccount ? (string) $data['password'] : Str::random(40),
            'role' => 'customer',
        ]);

        if ($createAccount) {
            Auth::login($user);
            $request->session()->regenerate();
            $request->session()->forget('checkout_guest_user_id');
        } else {
            $request->session()->put('checkout_guest_user_id', $user->id);
            $request->session()->regenerate();
        }

        return [
            'user' => $user,
            'guest_checkout' => ! $createAccount,
        ];
    }
}
