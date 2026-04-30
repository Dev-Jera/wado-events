<?php

namespace App\Models;

use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_CONFIRMED = TicketStatus::CONFIRMED->value;
    public const STATUS_USED = TicketStatus::USED->value;
    public const STATUS_CANCELLED = TicketStatus::CANCELLED->value;

    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_category_id',
        'holder_name',
        'payer_name',
        'ticket_code',
        'qr_code_path',
        'quantity',
        'unit_price',
        'total_amount',
        'payment_provider',
        'status',
        'gate_status',
        'reentry_count',
        'last_entry_at',
        'last_exit_at',
        'purchased_at',
        'used_at',
        'dismissed_at',
    ];

    public const GATE_STATUS_OUTSIDE = 'outside';
    public const GATE_STATUS_INSIDE  = 'inside';

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'reentry_count' => 'integer',
            'last_entry_at' => 'datetime',
            'last_exit_at'  => 'datetime',
            'purchased_at'  => 'datetime',
            'used_at'       => 'datetime',
            'dismissed_at'  => 'datetime',
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

    public function paymentTransaction(): HasOne
    {
        return $this->hasOne(PaymentTransaction::class, 'ticket_id');
    }

    public function scanLogs(): HasMany
    {
        return $this->hasMany(TicketScanLog::class);
    }

    public function latestScanLog(): HasOne
    {
        return $this->hasOne(TicketScanLog::class)->latestOfMany('scanned_at');
    }

    public function isTerminalStatus(): bool
    {
        return in_array($this->status, [self::STATUS_USED, self::STATUS_CANCELLED], true);
    }

    public function statusEnum(): TicketStatus
    {
        return TicketStatus::from((string) $this->status);
    }
}
