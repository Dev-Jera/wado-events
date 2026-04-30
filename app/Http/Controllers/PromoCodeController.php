<?php

namespace App\Http\Controllers;

use App\Services\Checkout\PromoCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function validate(Request $request, PromoCodeService $promoCodeService): JsonResponse
    {
        $data = $request->validate([
            'code'       => ['required', 'string', 'max:32'],
            'event_id'   => ['required', 'integer'],
            'subtotal'   => ['required', 'numeric', 'min:0'],
        ]);

        $result = $promoCodeService->validate(
            (string) $data['code'],
            (int) $data['event_id'],
            (float) $data['subtotal'],
        );

        if (! $result['ok']) {
            return response()->json(['ok' => false, 'message' => $result['message']], 422);
        }

        return response()->json([
            'ok'              => true,
            'message'         => $result['message'],
            'discount_type'   => $result['promo']->discount_type,
            'discount_value'  => (float) $result['promo']->discount_value,
            'discount_amount' => $result['discount_amount'],
            'final_total'     => $result['final_total'],
        ]);
    }
}
