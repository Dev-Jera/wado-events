<?php

namespace App\Policies;

use App\Models\User;

class PaymentTransactionPolicy
{
    public function manageAdminPayments(User $user): bool
    {
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
