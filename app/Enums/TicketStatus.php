<?php

namespace App\Enums;

enum TicketStatus: string
{
    case CONFIRMED = 'confirmed';
    case USED = 'used';
    case CANCELLED = 'cancelled';
}
