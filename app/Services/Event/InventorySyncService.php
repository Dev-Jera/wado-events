<?php

namespace App\Services\Event;

use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Support\Facades\Cache;

class InventorySyncService
{
    public function syncEventInventory(Event $event): void
    {
        $inventory = TicketCategory::query()
            ->where('event_id', $event->id)
            ->selectRaw('COALESCE(SUM(tickets_remaining), 0) AS remaining_total')
            ->first();

        $event->forceFill([
            'tickets_available' => (int) ($inventory?->remaining_total ?? 0),
        ])->save();

        // Flush caches that show event availability so the next request sees fresh data.
        // Gate portal caches (15 s TTL) are short-lived enough to expire on their own.
        Cache::tags(["event:{$event->id}"])->flush();
        Cache::forget('events:list');
        Cache::forget('home:featured_events');
    }
}
