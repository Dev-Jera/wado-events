<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'package',
        'event_date', 'attendance', 'message',
        'is_read', 'replied_at',
    ];

    protected $casts = [
        'is_read'    => 'boolean',
        'event_date' => 'date',
        'replied_at' => 'datetime',
    ];

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function getStatusAttribute(): string
    {
        if ($this->replied_at) return 'Replied';
        if ($this->is_read)   return 'Read';
        return 'New';
    }
}
