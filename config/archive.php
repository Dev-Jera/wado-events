<?php

return [
    'cutoff_months' => (int) env('ARCHIVE_CUTOFF_MONTHS', 6),
    'error_days' => (int) env('ARCHIVE_ERROR_DAYS', 90),
    'chunk_size' => (int) env('ARCHIVE_CHUNK_SIZE', 500),
];
