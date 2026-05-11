<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the notifiable entity that the notification belongs to.
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Mark the notification as unread.
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }

    /**
     * Determine if the notification is read.
     */
    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    /**
     * Determine if the notification is unread.
     */
    public function isUnread(): bool
    {
        return $this->read_at === null;
    }

    /**
     * Get the notification title from data.
     */
    public function getTitle(): string
    {
        return (string) ($this->data['title'] ?? 'Notification');
    }

    /**
     * Get the notification body from data.
     */
    public function getBody(): string
    {
        return (string) ($this->data['body'] ?? '');
    }

    /**
     * Get the notification type/category.
     */
    public function getCategory(): string
    {
        return explode('\\', (string) $this->type)[array_key_last(explode('\\', $this->type))] ?? 'Notification';
    }

    /**
     * Get color badge for notification type.
     */
    public function getColorBadge(): string
    {
        $category = $this->getCategory();

        return match ($category) {
            'warning' => 'warning',
            'danger', 'error' => 'danger',
            'success' => 'success',
            'info' => 'info',
            default => 'gray',
        };
    }
}
