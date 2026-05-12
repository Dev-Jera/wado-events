<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorLog extends Model
{
    protected $fillable = [
        'type',
        'severity',
        'message',
        'context',
        'user_id',
        'payment_id',
        'event_id',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'json',
        'resolved_at' => 'datetime',
    ];

    public static function log(string $type, string $severity, string $message, array $context = []): void
    {
        self::create([
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'context' => $context,
        ]);
    }

    public function resolve(): void
    {
        $this->update(['resolved_at' => now()]);
    }
}
