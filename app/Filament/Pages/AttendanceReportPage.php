<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Ticket;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class AttendanceReportPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Attendance Report';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 12;

    protected string $view = 'filament.pages.attendance-report-page';

    protected function getViewData(): array
    {
        $user = auth()->user();

        $events = Event::query()
            ->with('ticketCategories')
            ->when(
                $user?->isEventOwner() && ! $user->isAdmin(),
                fn (Builder $q) => $q->where('user_id', $user->id)
            )
            ->orderByDesc('starts_at')
            ->get()
            ->map(function (Event $event) {
                $capacity = (int) $event->ticketCategories->sum('ticket_count');
                $remaining = (int) $event->ticketCategories->sum('tickets_remaining');
                $sold = max($capacity - $remaining, 0);

                $scanned = (int) Ticket::where('event_id', $event->id)
                    ->where(function ($q): void {
                        $q->where('status', Ticket::STATUS_USED)
                          ->orWhereNotNull('used_at');
                    })
                    ->sum('quantity');

                $attendanceRate = $sold > 0 ? round($scanned / $sold * 100) : 0;
                $fillRate       = $capacity > 0 ? round($sold / $capacity * 100) : 0;

                return [
                    'title'          => $event->title,
                    'date'           => $event->starts_at->format('d M Y'),
                    'status'         => $event->live_status,
                    'capacity'       => $capacity,
                    'sold'           => $sold,
                    'scanned'        => $scanned,
                    'attendance_rate'=> $attendanceRate,
                    'fill_rate'      => $fillRate,
                ];
            });

        $totals = [
            'capacity' => $events->sum('capacity'),
            'sold'     => $events->sum('sold'),
            'scanned'  => $events->sum('scanned'),
            'rate'     => $events->sum('sold') > 0
                ? round($events->sum('scanned') / $events->sum('sold') * 100)
                : 0,
        ];

        return compact('events', 'totals');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canViewOperationsDashboard());
    }
}
