<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GateBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'created_by',
        'label',
        'price',
        'quantity',
        'ticket_size',
        'template',
        'notes',
        'status',
        'printed_at',
    ];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:2',
            'printed_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(GateTicket::class, 'batch_id');
    }

    public function isGenerated(): bool
    {
        return $this->tickets()->exists();
    }

    public function soldCount(): int
    {
        return $this->tickets()->where('status', 'sold')->count();
    }

    public function usedCount(): int
    {
        return $this->tickets()->where('status', 'used')->count();
    }
}
