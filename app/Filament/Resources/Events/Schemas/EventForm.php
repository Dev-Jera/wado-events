<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
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
                                                ->required()
                                                ->prefix('UGX')
                                                ->placeholder('50000'),

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
                                            $data['price'] = $get('../../is_free') ? 0 : ($data['price'] ?? 0);
                                            $data['tickets_remaining'] = $data['ticket_count'] ?? 0;
                                            return $data;
                                        })
                                        ->mutateRelationshipDataBeforeSaveUsing(function (array $data, callable $get): array {
                                            $data['price'] = $get('../../is_free') ? 0 : ($data['price'] ?? 0);
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
                                Section::make('Visibility & pricing')
                                    ->schema([
                                        Toggle::make('is_featured')
                                            ->label('Feature on homepage')
                                            ->helperText('Pinned to top of public events page.')
                                            ->onColor('primary'),

                                        Toggle::make('is_free')
                                            ->label('Free event')
                                            ->helperText('All ticket prices set to 0.')
                                            ->onColor('success'),
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
