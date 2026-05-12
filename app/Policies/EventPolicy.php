<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;

class EventPolicy
{
    public function accessGatePortal(User $user): bool
    {
        return $user->isGateStaff();
    }

    public function viewFinance(User $user, Event $event): bool
    {
        return $user->isAdmin();
    }
}
