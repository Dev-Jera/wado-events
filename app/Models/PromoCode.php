<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    protected $fillable = [
        'event_id',
        'code',
        'discount_type',
        'discount_value',
        'max_discount_amount',
        'min_order_amount',
        'max_uses',
        'uses',
        'expires_at',
        'is_active',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'discount_value'     => 'decimal:2',
            'max_discount_amount'=> 'decimal:2',
            'min_order_amount'   => 'decimal:2',
            'max_uses'           => 'integer',
            'uses'               => 'integer',
            'expires_at'         => 'datetime',
            'is_active'          => 'boolean',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function paymentTransactions(): HasMany
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    /** Calculate the discount amount for a given subtotal (unit_price × quantity). */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = $subtotal * ((float) $this->discount_value / 100);
            if ($this->max_discount_amount !== null) {
                $discount = min($discount, (float) $this->max_discount_amount);
            }
        } else {
            $discount = (float) $this->discount_value;
        }

        return min($discount, $subtotal); // never discount more than the total
    }

    /** Check whether this code can be used right now (ignores event/amount checks). */
    public function isUsable(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses !== null && $this->uses >= $this->max_uses) {
            return false;
        }

        return true;
    }
}
