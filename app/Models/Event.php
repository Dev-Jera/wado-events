<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'venue',
        'city',
        'country',
        'description',
        'starts_at',
        'ends_at',
        'ticket_price',
        'capacity',
        'tickets_available',
        'status',
        'image_url',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'ticket_price' => 'decimal:2',
            'is_featured' => 'boolean',
        ];
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function ticketCategories(): HasMany
    {
        return $this->hasMany(TicketCategory::class)->orderBy('sort_order');
    }

    public function artists(): HasMany
    {
        return $this->hasMany(EventArtist::class)->orderBy('sort_order');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(EventBookmark::class);
    }

    /**
     * Runtime lifecycle phase derived from timestamps.
     * Values: upcoming | live | ended | draft | cancelled
     */
    public function getLiveStatusAttribute(): string
    {
        if (in_array($this->status, ['draft', 'cancelled'], true)) {
            return $this->status;
        }

        $now = now();

        if ($now < $this->starts_at) {
            return 'upcoming';
        }

        if ($this->ends_at) {
            return $now <= $this->ends_at ? 'live' : 'ended';
        }

        // No ends_at: treat today's events as live, past-date events as ended
        return $this->starts_at->isToday() ? 'live' : 'ended';
    }
}
