<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use UnitEnum;

class ArchiveBrowserPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Archive Browser';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 91;

    protected string $view = 'filament.pages.archive-browser-page';

    public string $search = '';

    public string $recordType = 'all';

    public ?array $selectedRecord = null;

    public function getArchiveReady(): bool
    {
        try {
            return DB::connection('archive')->getSchemaBuilder()->hasTable('payment_transactions')
                && DB::connection('archive')->getSchemaBuilder()->hasTable('tickets')
                && DB::connection('archive')->getSchemaBuilder()->hasTable('error_logs');
        } catch (\Throwable) {
            return false;
        }
    }

    public function getArchiveRecords(): array
    {
        if (! $this->getArchiveReady()) {
            return [];
        }

        $records = [];
        $search = trim($this->search);
        $type = $this->recordType;

        if (in_array($type, ['all', 'payments'], true)) {
            $records = array_merge($records, $this->searchPayments($search));
        }

        if (in_array($type, ['all', 'tickets'], true)) {
            $records = array_merge($records, $this->searchTickets($search));
        }

        if (in_array($type, ['all', 'errors'], true)) {
            $records = array_merge($records, $this->searchErrors($search));
        }

        usort($records, fn (array $left, array $right) => strcmp((string) $right['created_at'], (string) $left['created_at']));

        return array_slice($records, 0, 120);
    }

    public function openRecord(string $source, int $recordId): void
    {
        if (! static::canAccess()) {
            abort(403);
        }

        try {
            $record = match ($source) {
                'payments' => DB::connection('archive')->table('payment_transactions')->where('id', $recordId)->first(),
                'tickets' => DB::connection('archive')->table('tickets')->where('id', $recordId)->first(),
                'errors' => DB::connection('archive')->table('error_logs')->where('id', $recordId)->first(),
                default => null,
            };
        } catch (\Throwable) {
            $record = null;
        }

        $this->selectedRecord = $record ? [
            'source' => $source,
            'id' => $recordId,
            'raw' => (array) $record,
            'pretty' => json_encode((array) $record, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ] : null;
    }

    public function clearSelection(): void
    {
        $this->selectedRecord = null;
    }

    private function searchPayments(string $search): array
    {
        $query = DB::connection('archive')->table('payment_transactions');

        if ($search !== '') {
            $this->applySearch($query, $search, [
                'idempotency_key',
                'holder_name',
                'phone_number',
                'provider_reference',
                'payment_provider',
                'status',
                'collector_reference',
            ]);
        }

        return $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(40)
            ->get()
            ->map(function ($row): array {
                $holder = trim((string) ($row->holder_name ?: ''));
                $title = $holder !== '' ? $holder : (string) ($row->provider_reference ?: $row->idempotency_key);

                return [
                    'source' => 'payments',
                    'id' => $row->id,
                    'title' => $title,
                    'subtitle' => strtoupper((string) ($row->payment_provider ?: 'payment')),
                    'status' => (string) $row->status,
                    'meta' => 'UGX ' . number_format((float) ($row->total_amount ?? 0), 0),
                    'created_at' => (string) $row->created_at,
                ];
            })
            ->all();
    }

    private function searchTickets(string $search): array
    {
        $query = DB::connection('archive')->table('tickets');

        if ($search !== '') {
            $this->applySearch($query, $search, [
                'ticket_code',
                'holder_name',
                'payer_name',
                'payment_provider',
                'status',
                'gate_status',
            ]);
        }

        return $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(40)
            ->get()
            ->map(function ($row): array {
                $holder = trim((string) ($row->holder_name ?: $row->payer_name ?: ''));
                $title = $holder !== '' ? $holder : (string) $row->ticket_code;

                return [
                    'source' => 'tickets',
                    'id' => $row->id,
                    'title' => $title,
                    'subtitle' => (string) $row->ticket_code,
                    'status' => (string) $row->status,
                    'meta' => strtoupper((string) ($row->payment_provider ?: 'ticket')),
                    'created_at' => (string) $row->created_at,
                ];
            })
            ->all();
    }

    private function searchErrors(string $search): array
    {
        $query = DB::connection('archive')->table('error_logs');

        if ($search !== '') {
            $this->applySearch($query, $search, [
                'type',
                'severity',
                'message',
                'user_id',
                'payment_id',
                'event_id',
            ]);
        }

        return $query->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(40)
            ->get()
            ->map(function ($row): array {
                return [
                    'source' => 'errors',
                    'id' => $row->id,
                    'title' => Str::limit((string) $row->message, 72),
                    'subtitle' => strtoupper((string) $row->type),
                    'status' => (string) $row->severity,
                    'meta' => $row->resolved_at ? 'resolved' : 'open',
                    'created_at' => (string) $row->created_at,
                ];
            })
            ->all();
    }

    private function applySearch($query, string $search, array $fields): void
    {
        $term = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $search) . '%';

        $query->where(function ($builder) use ($fields, $term, $search): void {
            foreach ($fields as $index => $field) {
                if ($index === 0) {
                    $builder->where($field, 'like', $term);
                } else {
                    $builder->orWhere($field, 'like', $term);
                }
            }

            if (is_numeric($search)) {
                $builder->orWhere('id', (int) $search);
                $builder->orWhere('event_id', (int) $search);
            }
        });
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }
}
