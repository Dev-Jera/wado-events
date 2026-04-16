<?php

namespace App\Enums;

enum PaymentTransactionStatus: string
{
    case INITIATED = 'INITIATED';
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case FAILED = 'FAILED';
    case REFUNDED = 'REFUNDED';
    case EXPIRED = 'EXPIRED';
}
