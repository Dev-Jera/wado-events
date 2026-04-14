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
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(5)->components([

                    // ── ROW 1 LEFT: Event details ─────────────────── (span 3)
                    Section::make('Event details')
                        ->description('Name, location, schedule and description')
                        ->icon('heroicon-o-calendar-days')
                        ->extraAttributes(['class' => 'ef-section'])
                        ->columnSpan(3)
                        ->components([
                            Grid::make(2)->components([
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
                                ->required()
                                ->rows(4)
                                ->placeholder('Describe your event…')
                                ->columnSpanFull(),
                        ]),

                    // ── ROW 1 RIGHT: Cover image ──────────────────── (span 2)
                    Section::make('Cover image')
                        ->description('Recommended 1200×630px')
                        ->icon('heroicon-o-photo')
                        ->extraAttributes(['class' => 'ef-section'])
                        ->columnSpan(2)
                        ->components([
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

                    // ── ROW 2 LEFT: Ticket categories ─────────────── (span 3)
                    Section::make('Ticket categories')
                        ->description('VIP, Ordinary, Early Bird — add as many as you need')
                        ->icon('heroicon-o-ticket')
                        ->extraAttributes(['class' => 'ef-section'])
                        ->columnSpan(3)
                        ->components([
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

                    // ── ROW 2 RIGHT: Settings + At a glance ──────── (span 2)
                    Section::make('Settings')
                        ->description('Visibility and pricing options')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->extraAttributes(['class' => 'ef-section'])
                        ->columnSpan(2)
                        ->components([
                            Toggle::make('is_featured')
                                ->label('Feature on homepage')
                                ->helperText('Pinned to public events page.')
                                ->onColor('primary'),

                            Toggle::make('is_free')
                                ->label('Free event')
                                ->helperText('All tickets set to 0')
                                ->onColor('success'),

                            Placeholder::make('glance_capacity')
                                ->label('Total capacity')
                                ->content(fn ($record) => $record
                                    ? number_format($record->ticketCategories->sum('ticket_count')) : '—'),

                            Placeholder::make('glance_sold')
                                ->label('Tickets sold')
                                ->content(fn ($record) => $record
                                    ? number_format(\App\Models\PaymentTransaction::where('event_id', $record->id)->where('status', 'CONFIRMED')->sum('quantity')) : '—'),

                            Placeholder::make('glance_revenue')
                                ->label('Revenue')
                                ->content(fn ($record) => $record
                                    ? 'UGX ' . number_format(\App\Models\PaymentTransaction::where('event_id', $record->id)->where('status', 'CONFIRMED')->sum('total_amount')) : '—'),
                        ]),

                    // ── ROW 3: Artists full width ─────────────────── (span 5)
                    Section::make('Artists')
                        ->description('Optional performers')
                        ->icon('heroicon-o-musical-note')
                        ->collapsible()
                        ->collapsed()
                        ->extraAttributes(['class' => 'ef-section'])
                        ->columnSpan(5)
                        ->components([
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
            ]);
    }
}
