<?php

namespace App\Observers;

use App\Models\Event;
use Illuminate\Support\Facades\Cache;

class EventObserver
{
    public function saved(Event $event): void
    {
        Cache::forget('events:list:all-published');
    }

    public function deleted(Event $event): void
    {
        Cache::forget('events:list:all-published');
    }
}
