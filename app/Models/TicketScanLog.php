<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketScanLog extends Model
{
    protected $fillable = [
        'ticket_id',
        'scan_type',
        'staff_user_id',
        'ticket_code',
        'scanned_payload',
        'device_id',
        'result',
        'message',
        'ip_address',
        'user_agent',
        'scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_user_id');
    }
}
