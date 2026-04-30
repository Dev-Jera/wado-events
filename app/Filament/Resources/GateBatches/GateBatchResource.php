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
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
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

    protected static string|UnitEnum|null $navigationGroup = 'Gate';

    protected static ?int $navigationSort = 20;

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

            Select::make('template')
                ->label('Ticket Design')
                ->options([
                    'classic'   => 'Classic — Clean blue, professional',
                    'vip_gold'  => 'VIP Gold — Dark luxury, gold accents',
                    'cultural'  => 'Cultural — Warm tones, African-inspired stripe',
                    'festival'  => 'Festival — Purple & pink, vibrant',
                    'corporate' => 'Corporate — Slate & cyan, minimal',
                ])
                ->default('classic')
                ->required()
                ->live(),

            Placeholder::make('template_preview')
                ->label('Design Preview')
                ->content(function (Get $get): HtmlString {
                    $tmpl = $get('template') ?? 'classic';

                    $themes = [
                        'classic' => [
                            'name'       => 'Classic',
                            'accent'     => '#2563eb',
                            'bodyBg'     => '#ffffff',
                            'stubBg'     => '#eff6ff',
                            'border'     => '#2563eb',
                            'dash'       => '#93c5fd',
                            'brand'      => '#2563eb',
                            'event'      => '#1e293b',
                            'meta'       => '#64748b',
                            'labelBg'    => '#2563eb',
                            'labelFg'    => '#ffffff',
                            'price'      => '#2563eb',
                            'code'       => '#1e293b',
                            'stripeType' => 'solid',
                        ],
                        'vip_gold' => [
                            'name'       => 'VIP Gold',
                            'accent'     => '#f59e0b',
                            'bodyBg'     => '#0f172a',
                            'stubBg'     => '#1e293b',
                            'border'     => '#f59e0b',
                            'dash'       => '#334155',
                            'brand'      => '#f59e0b',
                            'event'      => '#f8fafc',
                            'meta'       => '#94a3b8',
                            'labelBg'    => '#f59e0b',
                            'labelFg'    => '#0f172a',
                            'price'      => '#f59e0b',
                            'code'       => '#94a3b8',
                            'stripeType' => 'solid',
                        ],
                        'cultural' => [
                            'name'       => 'Cultural',
                            'accent'     => '#c2510f',
                            'bodyBg'     => '#fdf8f0',
                            'stubBg'     => '#fff7ed',
                            'border'     => '#c2510f',
                            'dash'       => '#fed7aa',
                            'brand'      => '#c2510f',
                            'event'      => '#1c1917',
                            'meta'       => '#57534e',
                            'labelBg'    => '#c2510f',
                            'labelFg'    => '#ffffff',
                            'price'      => '#c2510f',
                            'code'       => '#44403c',
                            'stripeType' => 'kente',
                        ],
                        'festival' => [
                            'name'       => 'Festival',
                            'accent'     => '#7c3aed',
                            'bodyBg'     => '#ffffff',
                            'stubBg'     => '#faf5ff',
                            'border'     => '#7c3aed',
                            'dash'       => '#c4b5fd',
                            'brand'      => '#7c3aed',
                            'event'      => '#1e1b4b',
                            'meta'       => '#6b7280',
                            'labelBg'    => '#ec4899',
                            'labelFg'    => '#ffffff',
                            'price'      => '#7c3aed',
                            'code'       => '#4c1d95',
                            'stripeType' => 'duo',
                        ],
                        'corporate' => [
                            'name'       => 'Corporate',
                            'accent'     => '#0f172a',
                            'bodyBg'     => '#f8fafc',
                            'stubBg'     => '#f1f5f9',
                            'border'     => '#334155',
                            'dash'       => '#cbd5e1',
                            'brand'      => '#0891b2',
                            'event'      => '#0f172a',
                            'meta'       => '#64748b',
                            'labelBg'    => '#0f172a',
                            'labelFg'    => '#ffffff',
                            'price'      => '#0891b2',
                            'code'       => '#334155',
                            'stripeType' => 'solid',
                        ],
                    ];

                    $t = $themes[$tmpl] ?? $themes['classic'];

                    $stripe = match ($t['stripeType']) {
                        'kente' => '<div style="height:7px;background:linear-gradient(to right,#c2510f 12%,#92400e 24%,#fbbf24 36%,#15803d 50%,#c2510f 62%,#92400e 75%,#fbbf24 88%,#0c4a6e 100%);"></div>',
                        'duo'   => '<div style="height:7px;background:linear-gradient(to right,#7c3aed 62%,#ec4899 38%);"></div>',
                        default => '<div style="height:7px;background:' . $t['accent'] . ';"></div>',
                    };

                    $vipLabel = $tmpl === 'vip_gold'
                        ? '<div style="font-size:8px;font-weight:800;letter-spacing:.12em;color:' . $t['price'] . ';margin-bottom:3px;">VIP</div>'
                        : '';

                    $qrColor = $tmpl === 'vip_gold' ? '#f59e0b' : $t['border'];

                    $html = '
                    <div style="font-family:system-ui,sans-serif;max-width:520px;">
                        <div style="display:table;width:100%;border:1.5px solid ' . $t['border'] . ';border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.12);">
                            <!-- Body -->
                            <div style="display:table-cell;width:68%;background:' . $t['bodyBg'] . ';border-right:2px dashed ' . $t['dash'] . ';vertical-align:top;">
                                ' . $stripe . '
                                <div style="padding:8px 10px 10px;">
                                    <div style="font-size:7px;font-weight:700;letter-spacing:.1em;color:' . $t['brand'] . ';text-transform:uppercase;margin-bottom:3px;">WADO Events</div>
                                    <div style="font-size:11px;font-weight:800;color:' . $t['event'] . ';margin-bottom:4px;line-height:1.2;">Your Event Name</div>
                                    <span style="display:inline-block;font-size:7px;font-weight:700;background:' . $t['labelBg'] . ';color:' . $t['labelFg'] . ';padding:1px 6px;border-radius:3px;text-transform:uppercase;letter-spacing:.04em;margin-bottom:5px;">General Entry</span>
                                    <div style="font-size:7.5px;color:' . $t['meta'] . ';line-height:1.7;">
                                        <span style="font-weight:700;color:' . $t['event'] . ';">Date:</span> 27 Apr 2026<br>
                                        <span style="font-weight:700;color:' . $t['event'] . ';">Venue:</span> Kampala Serena Hotel
                                    </div>
                                </div>
                            </div>
                            <!-- Stub -->
                            <div style="display:table-cell;width:32%;background:' . $t['stubBg'] . ';vertical-align:middle;text-align:center;padding:8px 6px;">
                                ' . $vipLabel . '
                                <div style="font-size:9px;font-weight:800;color:' . $t['price'] . ';margin-bottom:5px;">UGX 50,000</div>
                                <div style="width:38px;height:38px;margin:0 auto 5px;border:2px solid ' . $qrColor . ';border-radius:3px;display:flex;align-items:center;justify-content:center;">
                                    <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                                        <rect x="1" y="1" width="10" height="10" rx="1" fill="' . $qrColor . '" opacity=".9"/>
                                        <rect x="17" y="1" width="10" height="10" rx="1" fill="' . $qrColor . '" opacity=".9"/>
                                        <rect x="1" y="17" width="10" height="10" rx="1" fill="' . $qrColor . '" opacity=".9"/>
                                        <rect x="3" y="3" width="6" height="6" fill="' . $t['stubBg'] . '"/>
                                        <rect x="19" y="3" width="6" height="6" fill="' . $t['stubBg'] . '"/>
                                        <rect x="3" y="19" width="6" height="6" fill="' . $t['stubBg'] . '"/>
                                        <rect x="17" y="17" width="3" height="3" fill="' . $qrColor . '" opacity=".8"/>
                                        <rect x="22" y="17" width="3" height="3" fill="' . $qrColor . '" opacity=".8"/>
                                        <rect x="17" y="22" width="3" height="3" fill="' . $qrColor . '" opacity=".8"/>
                                        <rect x="22" y="22" width="3" height="3" fill="' . $qrColor . '" opacity=".8"/>
                                    </svg>
                                </div>
                                <div style="font-size:6px;font-family:monospace;color:' . $t['code'] . ';letter-spacing:.04em;">GP-01-AB12CD34</div>
                            </div>
                        </div>
                        <div style="margin-top:6px;font-size:10px;color:#94a3b8;text-align:center;">
                            ' . $t['name'] . ' template — actual ticket will fill in your event details
                        </div>
                    </div>';

                    return new HtmlString($html);
                }),

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

                TextColumn::make('template')
                    ->label('Design')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'vip_gold'  => 'VIP Gold',
                        'cultural'  => 'Cultural',
                        'festival'  => 'Festival',
                        'corporate' => 'Corporate',
                        default     => 'Classic',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'vip_gold'  => 'warning',
                        'cultural'  => 'danger',
                        'festival'  => 'primary',
                        'corporate' => 'gray',
                        default     => 'info',
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
                    ->action(function (GateBatch $record, \Livewire\Component $livewire): void {
                        abort_unless(static::userCanModifyBatch($record), 403);
                        app(GatePrintTicketService::class)->generateTickets($record);
                        Notification::make()->title('Tickets generated. You can now download the PDF.')->success()->send();
                        $livewire->dispatch('$refresh');
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
