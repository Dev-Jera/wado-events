<?php

namespace App\Services\Event;

use App\Models\Event;
use App\Models\TicketCategory;

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
    }
}
