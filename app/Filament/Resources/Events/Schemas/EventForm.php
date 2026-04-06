<?php

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Category;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Details')
                    ->components([
                        Grid::make(2)
                            ->components([
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255),
                                        Textarea::make('description')
                                            ->rows(3),
                                    ])
                                    ->createOptionUsing(function (array $data): int {
                                        return Category::create($data)->getKey();
                                    }),
                                TextInput::make('venue')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('status')
                                    ->options([
                                        'draft' => 'Draft',
                                        'published' => 'Published',
                                        'cancelled' => 'Cancelled',
                                    ])
                                    ->default('draft')
                                    ->required(),
                                TextInput::make('city')
                                    ->required()
                                    ->maxLength(120),
                                TextInput::make('country')
                                    ->required()
                                    ->maxLength(120),
                                DateTimePicker::make('starts_at')
                                    ->required()
                                    ->native(false),
                                DateTimePicker::make('ends_at')
                                    ->native(false),
                            ]),
                        Textarea::make('description')
                            ->required()
                            ->rows(6),
                    ]),
                Section::make('Media & Settings')
                    ->components([
                        Grid::make(2)
                            ->components([
                                FileUpload::make('image_url')
                                    ->label('Event Image')
                                    ->disk('public')
                                    ->directory('event-images')
                                    ->image()
                                    ->visibility('public')
                                    ->moveFiles()
                                    ->helperText('Upload an image from your computer.'),
                                Grid::make(2)
                                    ->components([
                                        Toggle::make('is_featured')
                                            ->label('Featured event'),
                                        Toggle::make('is_free')
                                            ->label('Free event'),
                                    ]),
                            ]),
                    ]),
                Section::make('Ticket Categories')
                    ->description('Add as many ticket types as you need, like VIP or Ordinary.')
                    ->components([
                        Repeater::make('ticketCategories')
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->defaultItems(2)
                            ->reorderableWithButtons()
                            ->columns(3)
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(100),
                                TextInput::make('price')
                                    ->numeric()
                                    ->default(0)
                                    ->required()
                                    ->prefix('UGX'),
                                TextInput::make('ticket_count')
                                    ->label('Tickets')
                                    ->integer()
                                    ->minValue(1)
                                    ->required(),
                                TextInput::make('tickets_remaining')
                                    ->integer()
                                    ->minValue(0)
                                    ->default(0)
                                    ->required()
                                    ->label('Remaining'),
                                Textarea::make('description')
                                    ->rows(2)
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
                Section::make('Artists')
                    ->description('Optional. Leave empty for events that do not feature artists.')
                    ->components([
                        Repeater::make('artists')
                            ->relationship()
                            ->orderColumn('sort_order')
                            ->reorderableWithButtons()
                            ->schema([
                                TextInput::make('name')
                                    ->required()
                                    ->maxLength(120),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }
}
