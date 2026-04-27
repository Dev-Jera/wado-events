<?php

namespace App\Filament\Resources\GateBatches;

use App\Filament\Resources\GateBatches\Pages\CreateGateBatch;
use App\Filament\Resources\GateBatches\Pages\ListGateBatches;
use App\Models\Event;
use App\Models\GateBatch;
use App\Models\User;
use App\Services\GatePrint\GatePrintTicketService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class GateBatchResource extends Resource
{
    protected static ?string $model = GateBatch::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?string $navigationLabel = 'Gate Tickets';

    protected static ?string $modelLabel = 'Ticket Batch';

    protected static ?string $pluralModelLabel = 'Gate Ticket Batches';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 8;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with('event');
        $user  = auth()->user();

        // Event owners see only batches for their own events
        if ($user?->isEventOwner() && ! $user->isAdmin()) {
            $query->whereHas('event', fn (Builder $q) => $q->where('user_id', $user->id));
        }

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('event_id')
                ->label('Event')
                ->options(function (): array {
                    $user  = auth()->user();
                    $query = Event::query()->orderByDesc('starts_at');

                    // Event owners can only create batches for their own events
                    if ($user?->isEventOwner() && ! $user->isAdmin()) {
                        $query->where('user_id', $user->id);
                    }

                    return $query->pluck('title', 'id')->toArray();
                })
                ->searchable()
                ->required(),

            TextInput::make('label')
                ->label('Category / Label')
                ->placeholder('e.g. General Entry, VIP, VVIP')
                ->required()
                ->maxLength(255),

            TextInput::make('price')
                ->label('Ticket Price (UGX)')
                ->numeric()
                ->default(0)
                ->minValue(0)
                ->prefix('UGX'),

            TextInput::make('quantity')
                ->label('Number of Tickets')
                ->numeric()
                ->required()
                ->minValue(1)
                ->maxValue(10000),

            Select::make('ticket_size')
                ->label('Ticket Size')
                ->options([
                    'small'    => 'Small  — 4 per A4 page',
                    'standard' => 'Standard — 2 per A4 page',
                    'large'    => 'Large — 1 per A4 page',
                ])
                ->default('small')
                ->required(),

            Textarea::make('notes')
                ->label('Internal Notes')
                ->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->limit(32),

                TextColumn::make('label')
                    ->label('Category')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Qty')
                    ->sortable(),

                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => $state > 0 ? 'UGX ' . number_format((float) $state, 0) : 'Free')
                    ->sortable(),

                TextColumn::make('ticket_size')
                    ->label('Size')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'large'    => 'info',
                        'standard' => 'warning',
                        default    => 'gray',
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'closed' => 'gray',
                        'voided' => 'danger',
                        default  => 'warning',
                    }),

                TextColumn::make('tickets_count')
                    ->label('Generated')
                    ->counts('tickets')
                    ->badge()
                    ->color('success'),

                TextColumn::make('printed_at')
                    ->label('Generated At')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('generate')
                    ->label('Generate')
                    ->icon(Heroicon::OutlinedSparkles)
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Ticket Codes')
                    ->modalDescription(fn (GateBatch $record) => "Generate {$record->quantity} unique signed tickets for \"{$record->label}\". Once generated the codes cannot be changed.")
                    ->action(function (GateBatch $record): void {
                        abort_unless(static::userCanModifyBatch($record), 403);
                        app(GatePrintTicketService::class)->generateTickets($record);
                        Notification::make()->title('Tickets generated. You can now download the PDF.')->success()->send();
                    })
                    ->visible(fn (GateBatch $record): bool =>
                        $record->status === 'draft' && static::userCanModifyBatch($record)
                    ),

                Action::make('download_pdf')
                    ->label('Print PDF')
                    ->icon(Heroicon::OutlinedPrinter)
                    ->color('success')
                    ->url(fn (GateBatch $record) => route('gate-batches.download-pdf', $record->id))
                    ->openUrlInNewTab()
                    ->visible(fn (GateBatch $record): bool =>
                        $record->status !== 'voided' && static::userCanModifyBatch($record)
                    ),

                Action::make('void')
                    ->label('Void')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Void This Batch')
                    ->modalDescription('All tickets in this batch will be marked void and can no longer be used for entry. This cannot be undone.')
                    ->action(function (GateBatch $record): void {
                        abort_unless(static::userCanModifyBatch($record), 403);
                        $record->tickets()->update(['status' => 'void']);
                        $record->update(['status' => 'voided']);
                        Notification::make()->title('Batch voided.')->warning()->send();
                    })
                    ->visible(fn (GateBatch $record): bool =>
                        ! in_array($record->status, ['voided', 'closed'], true) && static::userCanModifyBatch($record)
                    ),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListGateBatches::route('/'),
            'create' => CreateGateBatch::route('/create'),
        ];
    }

    // ── Authorization ──────────────────────────────────────────────────

    /**
     * Only admins and event owners see this section — gate agents do not.
     */
    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->isSuperAdmin() || $user?->isAdmin() || $user?->isEventOwner());
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }

    /**
     * Can the current user generate/void/download this specific batch?
     * Admins can touch any batch; event owners only touch their own events.
     */
    public static function userCanModifyBatch(GateBatch $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin() || $user->isAdmin()) {
            return true;
        }

        if ($user->isEventOwner()) {
            $eventOwnerId = $record->event?->user_id
                ?? Event::find($record->event_id)?->user_id;

            return (int) $eventOwnerId === (int) $user->id;
        }

        return false;
    }
}
