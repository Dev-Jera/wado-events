<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'email',
        'ip_address',
        'succeeded',
        'user_agent',
        'user_id',
        'failure_reason',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'succeeded'    => 'boolean',
            'attempted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Count failed attempts from a given IP in the last N minutes.
     */
    public static function recentFailureCountForIp(string $ip, int $minutes = 10): int
    {
        return static::query()
            ->where('ip_address', $ip)
            ->where('succeeded', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();
    }
}
