<?php

namespace App\Policies;

use App\Models\User;

class EventPolicy
{
    public function accessGatePortal(User $user): bool
    {
        return $user->isGateStaff();
    }
}
