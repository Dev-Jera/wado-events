<?php

namespace App\Policies;

use App\Models\User;

class TicketPolicy
{
    public function verify(User $user): bool
    {
        return $user->isGateStaff();
    }
}
