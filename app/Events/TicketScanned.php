<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketScanned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int    $eventId,
        public readonly string $ticketCode,
        public readonly string $holder,
        public readonly string $category,
        public readonly string $result,
        public readonly bool   $ok,
        public readonly string $staffName,
        public readonly string $scannedAt,
        public readonly string $deviceId,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('event.' . $this->eventId . '.scans')];
    }

    public function broadcastAs(): string
    {
        return 'TicketScanned';
    }
}
