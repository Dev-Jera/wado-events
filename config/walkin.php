<?php

return [
    // Max tickets one agent can sell in a single cash/POS walk-in transaction.
    'cash_max_tickets_per_sale' => (int) env('WALKIN_CASH_MAX_TICKETS_PER_SALE', 6),

    // Max UGX one agent can collect via cash/POS per day; set 0 to disable.
    'cash_daily_limit_ugx' => (float) env('WALKIN_CASH_DAILY_LIMIT_UGX', 2000000),
];
