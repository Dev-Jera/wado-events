<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventBookmarkController extends Controller
{
    public function toggle(Request $request, Event $event): JsonResponse
    {
        $user = $request->user();

        $existing = $user->bookmarks()->where('event_id', $event->id)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['bookmarked' => false]);
        }

        $user->bookmarks()->create(['event_id' => $event->id]);

        return response()->json(['bookmarked' => true]);
    }
}
