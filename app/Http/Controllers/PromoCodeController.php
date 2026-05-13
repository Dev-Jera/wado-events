<?php

namespace App\Http\Controllers;

use App\Services\Checkout\PromoCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PromoCodeController extends Controller
{
    public function validate(Request $request, PromoCodeService $promoCodeService): JsonResponse
    {
        $request->merge([
            'code' => Str::upper(trim((string) $request->input('code'))),
        ]);

        $data = $request->validate([
            'code'       => ['required', 'string', 'max:32', 'regex:/^[A-Z0-9_-]+$/'],
            'event_id'   => ['required', 'integer', 'exists:events,id'],
            'subtotal'   => ['required', 'numeric', 'min:0'],
        ], [
            'code.regex' => 'Promo code may contain only letters, numbers, hyphen, and underscore.',
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
