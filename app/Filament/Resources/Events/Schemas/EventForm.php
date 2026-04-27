<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Category;
use App\Models\PaymentTransaction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Tabs::make()
                ->contained()
                ->tabs([

                    // ── Tab 1: Event Details ──────────────────────────────
                    Tab::make('Event details')
                        ->icon('heroicon-o-calendar-days')
                        ->schema([
                            Section::make()
                                ->description('BASIC INFO')
                                ->schema([
                                    Grid::make(2)->schema([
                                        TextInput::make('title')
                                            ->label('Event title')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('e.g. Live Music Night'),

                                        Select::make('category_id')
                                            ->label('Category')
                                            ->options(fn () => Category::pluck('name', 'id'))
                                            ->native(false)
                                            ->required(),

                                        TextInput::make('venue')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('e.g. Kampala Serena Hotel'),

                                        Select::make('status')
                                            ->options([
                                                'draft'     => 'Draft',
                                                'published' => 'Published',
                                                'cancelled' => 'Cancelled',
                                            ])
                                            ->default('draft')
                                            ->required()
                                            ->native(false)
                                            ->selectablePlaceholder(false),

                                        TextInput::make('city')
                                            ->required()
                                            ->maxLength(120)
                                            ->placeholder('e.g. Kampala'),

                                        TextInput::make('country')
                                            ->required()
                                            ->maxLength(120)
                                            ->placeholder('e.g. Uganda'),

                                        DateTimePicker::make('starts_at')
                                            ->label('Starts at')
                                            ->required()
                                            ->native(false)
                                            ->displayFormat('M d, Y H:i'),

                                        DateTimePicker::make('ends_at')
                                            ->label('Ends at')
                                            ->native(false)
                                            ->displayFormat('M d, Y H:i'),
                                    ]),

                                    Textarea::make('description')
                                        ->label('About this event')
                                        ->required()
                                        ->rows(4)
                                        ->placeholder('Describe your event…')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ── Tab 2: Cover Image ────────────────────────────────
                    Tab::make('Cover image')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Section::make()
                                ->description('Recommended 1200×630px')
                                ->schema([
                                    FileUpload::make('image_url')
                                        ->label(false)
                                        ->disk('public')
                                        ->directory('event-images')
                                        ->image()
                                        ->imageEditor()
                                        ->visibility('public')
                                        ->moveFiles()
                                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                        ->helperText('JPG, PNG or WebP'),
                                ]),
                        ]),

                    // ── Tab 3: Ticket Categories ──────────────────────────
                    Tab::make('Ticket categories')
                        ->icon('heroicon-o-ticket')
                        ->schema([
                            Section::make('Is this a free event?')
                                ->description('Turn this on if attendees do not need to pay.')
                                ->schema([
                                    Toggle::make('is_free')
                                        ->label('Free event — no payment required')
                                        ->helperText('When enabled, all ticket prices are set to 0 and payment fields are hidden.')
                                        ->onColor('success')
                                        ->live(),
                                ]),

                            Section::make()
                                ->schema([
                                    Repeater::make('ticketCategories')
                                        ->relationship()
                                        ->orderColumn('sort_order')
                                        ->defaultItems(2)
                                        ->reorderableWithButtons()
                                        ->addActionLabel('+ Add ticket category')
                                        ->columns(3)
                                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                                        ->schema([
                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(100)
                                                ->placeholder('e.g. VIP'),

                                            TextInput::make('price')
                                                ->numeric()
                                                ->default(0)
                                                ->prefix('UGX')
                                                ->placeholder('50000')
                                                ->hidden(fn (callable $get): bool => (bool) $get('../../../../is_free'))
                                                ->required(fn (callable $get): bool => ! (bool) $get('../../../../is_free')),

                                            TextInput::make('ticket_count')
                                                ->label('Tickets')
                                                ->integer()
                                                ->minValue(1)
                                                ->required()
                                                ->placeholder('100'),

                                            TextInput::make('tickets_remaining')
                                                ->integer()
                                                ->minValue(0)
                                                ->default(0)
                                                ->required()
                                                ->label('Remaining'),

                                            Textarea::make('description')
                                                ->rows(2)
                                                ->placeholder('e.g. Front row seating, lounge access…')
                                                ->columnSpan(2),
                                        ])
                                        ->mutateRelationshipDataBeforeCreateUsing(function (array $data, callable $get): array {
                                            $data['price'] = $get('../../../../is_free') ? 0 : ($data['price'] ?? 0);
                                            $data['tickets_remaining'] = $data['ticket_count'] ?? 0;
                                            return $data;
                                        })
                                        ->mutateRelationshipDataBeforeSaveUsing(function (array $data, callable $get): array {
                                            $data['price'] = $get('../../../../is_free') ? 0 : ($data['price'] ?? 0);
                                            $data['tickets_remaining'] = $data['ticket_count'] ?? ($data['tickets_remaining'] ?? 0);
                                            return $data;
                                        }),
                                ]),
                        ]),

                    // ── Tab 4: Settings ───────────────────────────────────
                    Tab::make('Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Grid::make(2)->schema([
                                Section::make('Visibility')
                                    ->schema([
                                        Toggle::make('is_featured')
                                            ->label('Feature on homepage')
                                            ->helperText('Pinned to top of public events page.')
                                            ->onColor('primary'),
                                    ]),

                                Section::make('At a glance')
                                    ->schema([
                                        Placeholder::make('stat_capacity')
                                            ->label('Total capacity')
                                            ->content(fn ($record) => $record
                                                ? number_format($record->ticketCategories->sum('ticket_count')) : '—'),

                                        Placeholder::make('stat_sold')
                                            ->label('Tickets sold')
                                            ->content(fn ($record) => $record
                                                ? number_format(\App\Models\PaymentTransaction::where('event_id', $record->id)->where('status', 'CONFIRMED')->sum('quantity')) : '—'),

                                        Placeholder::make('stat_revenue')
                                            ->label('Revenue')
                                            ->content(fn ($record) => $record
                                                ? 'UGX ' . number_format(\App\Models\PaymentTransaction::where('event_id', $record->id)->where('status', 'CONFIRMED')->sum('total_amount')) : '—'),
                                    ]),
                            ]),

                            Section::make('Revenue by category')
                                ->description('Sales and revenue broken down per ticket tier.')
                                ->icon('heroicon-o-chart-bar')
                                ->collapsible()
                                ->collapsed()
                                ->schema([
                                    Placeholder::make('category_revenue')
                                        ->label('')
                                        ->content(function ($record): HtmlString {
                                            if (! $record) {
                                                return new HtmlString('<p style="color:#64748b;font-size:.85rem;padding:.5rem 0;">Save the event first to see category stats.</p>');
                                            }

                                            $categories = $record->ticketCategories;

                                            if ($categories->isEmpty()) {
                                                return new HtmlString('<p style="color:#64748b;font-size:.85rem;padding:.5rem 0;">No ticket categories defined yet.</p>');
                                            }

                                            $rows        = '';
                                            $grandSold   = 0;
                                            $grandRevenue = 0.0;

                                            foreach ($categories as $cat) {
                                                $sold    = max((int) $cat->ticket_count - (int) $cat->tickets_remaining, 0);
                                                $revenue = (float) PaymentTransaction::where('ticket_category_id', $cat->id)
                                                    ->where('status', PaymentTransaction::STATUS_CONFIRMED)
                                                    ->sum('total_amount');
                                                $pct     = $cat->ticket_count > 0 ? round($sold / $cat->ticket_count * 100) : 0;

                                                $grandSold    += $sold;
                                                $grandRevenue += $revenue;

                                                $bar = "<div style='height:4px;border-radius:2px;background:#e2e8f0;margin-top:2px;'>"
                                                     . "<div style='height:4px;border-radius:2px;background:#2563eb;width:{$pct}%;'></div>"
                                                     . "</div>";

                                                $rows .= "<tr>"
                                                    . "<td style='padding:.55rem .75rem;font-size:.83rem;color:#1e293b;'>{$cat->name}{$bar}</td>"
                                                    . "<td style='padding:.55rem .75rem;font-size:.83rem;color:#64748b;text-align:center;'>" . number_format($cat->ticket_count) . "</td>"
                                                    . "<td style='padding:.55rem .75rem;font-size:.83rem;font-weight:600;color:#1e293b;text-align:center;'>" . number_format($sold) . " <span style='font-weight:400;color:#94a3b8;font-size:.75rem;'>({$pct}%)</span></td>"
                                                    . "<td style='padding:.55rem .75rem;font-size:.83rem;font-weight:700;color:#2563eb;text-align:right;'>UGX " . number_format($revenue, 0) . "</td>"
                                                    . "</tr>";
                                            }

                                            $html = "<div style='overflow-x:auto;'>"
                                                . "<table style='width:100%;border-collapse:collapse;border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;font-family:inherit;'>"
                                                . "<thead><tr style='background:#f8fafc;'>"
                                                . "<th style='padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;text-align:left;border-bottom:1px solid #e2e8f0;'>Category</th>"
                                                . "<th style='padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;text-align:center;border-bottom:1px solid #e2e8f0;'>Capacity</th>"
                                                . "<th style='padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;text-align:center;border-bottom:1px solid #e2e8f0;'>Sold</th>"
                                                . "<th style='padding:.5rem .75rem;font-size:.7rem;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.05em;text-align:right;border-bottom:1px solid #e2e8f0;'>Revenue</th>"
                                                . "</tr></thead>"
                                                . "<tbody>{$rows}</tbody>"
                                                . "<tfoot><tr style='background:#f8fafc;border-top:2px solid #e2e8f0;'>"
                                                . "<td colspan='2' style='padding:.55rem .75rem;font-size:.82rem;font-weight:700;color:#1e293b;'>Total</td>"
                                                . "<td style='padding:.55rem .75rem;font-size:.82rem;font-weight:700;color:#1e293b;text-align:center;'>" . number_format($grandSold) . "</td>"
                                                . "<td style='padding:.55rem .75rem;font-size:.82rem;font-weight:700;color:#2563eb;text-align:right;'>UGX " . number_format($grandRevenue, 0) . "</td>"
                                                . "</tr></tfoot>"
                                                . "</table></div>";

                                            return new HtmlString($html);
                                        }),
                                ]),

                            Section::make('Artists')
                                ->description('Optional performers')
                                ->icon('heroicon-o-musical-note')
                                ->collapsible()
                                ->collapsed()
                                ->schema([
                                    Repeater::make('artists')
                                        ->relationship()
                                        ->orderColumn('sort_order')
                                        ->reorderableWithButtons()
                                        ->addActionLabel('+ Add artist')
                                        ->columns(3)
                                        ->schema([
                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(120)
                                                ->placeholder('Artist or performer name'),
                                        ]),
                                ]),
                        ]),

                ]),

        ]);
    }
}
