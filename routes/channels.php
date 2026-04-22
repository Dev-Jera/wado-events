<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
 * Gate scan feed: accessible to super admins and any gate agent assigned to the event.
 * Used by TicketScanned broadcast event.
 */
Broadcast::channel('event.{eventId}.scans', function (User $user, int $eventId): bool {
    return $user->isSuperAdmin() || $user->canAccessGateEvent($eventId);
});
