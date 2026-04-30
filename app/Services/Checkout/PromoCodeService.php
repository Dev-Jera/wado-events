<?php

namespace App\Services\Checkout;

use App\Models\PromoCode;

class PromoCodeService
{
    /**
     * Validate a promo code and return discount info, or an error string.
     *
     * @return array{ok: bool, message: string, promo?: PromoCode, discount_amount?: float, final_total?: float}
     */
    public function validate(string $code, int $eventId, float $subtotal): array
    {
        $promo = PromoCode::query()
            ->where('code', strtoupper(trim($code)))
            ->first();

        if (! $promo) {
            return ['ok' => false, 'message' => 'Invalid promo code.'];
        }

        if (! $promo->isUsable()) {
            return ['ok' => false, 'message' => 'This promo code is no longer valid.'];
        }

        // Code is scoped to a specific event — must match
        if ($promo->event_id !== null && $promo->event_id !== $eventId) {
            return ['ok' => false, 'message' => 'This promo code is not valid for this event.'];
        }

        if ($promo->min_order_amount !== null && $subtotal < (float) $promo->min_order_amount) {
            return [
                'ok'      => false,
                'message' => 'Minimum order of UGX ' . number_format((float) $promo->min_order_amount, 0) . ' required.',
            ];
        }

        $discountAmount = $promo->calculateDiscount($subtotal);

        return [
            'ok'              => true,
            'message'         => 'Promo code applied!',
            'promo'           => $promo,
            'discount_amount' => $discountAmount,
            'final_total'     => max(0, $subtotal - $discountAmount),
        ];
    }

    /**
     * Atomically increment the usage counter.
     * Call this inside a DB transaction after locking the promo code row.
     */
    public function incrementUses(PromoCode $promo): void
    {
        PromoCode::query()->lockForUpdate()->findOrFail($promo->id)->increment('uses');
    }
}
