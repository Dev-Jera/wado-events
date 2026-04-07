<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'ticket_count',
        'tickets_remaining',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function tickets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
