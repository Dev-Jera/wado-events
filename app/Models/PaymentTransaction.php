<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    public const STATUS_INITIATED = 'INITIATED';
    public const STATUS_PENDING = 'PENDING';
    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_FAILED = 'FAILED';
    public const STATUS_REFUNDED = 'REFUNDED';
    public const STATUS_EXPIRED = 'EXPIRED';

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_category_id',
        'ticket_id',
        'holder_name',
        'idempotency_key',
        'payment_provider',
        'phone_number',
        'quantity',
        'unit_price',
        'total_amount',
        'currency',
        'status',
        'provider_reference',
        'provider_status',
        'provider_payload',
        'webhook_payload',
        'expires_at',
        'callback_received_at',
        'confirmed_at',
        'failed_at',
        'refunded_at',
        'ticket_issued_at',
        'last_error',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'provider_payload' => 'array',
            'webhook_payload' => 'array',
            'expires_at' => 'datetime',
            'callback_received_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'failed_at' => 'datetime',
            'refunded_at' => 'datetime',
            'ticket_issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketCategory(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function isTerminal(): bool
    {
        return in_array($this->status, [
            self::STATUS_CONFIRMED,
            self::STATUS_FAILED,
            self::STATUS_REFUNDED,
        ], true);
    }
}
