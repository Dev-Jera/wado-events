<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GateTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'event_id',
        'ticket_code',
        'qr_payload',
        'status',
        'sold_at',
        'used_at',
        'used_by',
    ];

    protected function casts(): array
    {
        return [
            'sold_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(GateBatch::class, 'batch_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
