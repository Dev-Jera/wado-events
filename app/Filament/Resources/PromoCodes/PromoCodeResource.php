<?php

namespace App\Filament\Resources\PromoCodes;

use App\Filament\Resources\PromoCodes\Pages\CreatePromoCode;
use App\Filament\Resources\PromoCodes\Pages\EditPromoCode;
use App\Filament\Resources\PromoCodes\Pages\ListPromoCodes;
use App\Models\Event;
use App\Models\PromoCode;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Promo Codes';

    protected static ?string $modelLabel = 'Promo Code';

    protected static ?string $pluralModelLabel = 'Promo Codes';

    protected static string|\UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label('Code')
                ->required()
                ->maxLength(32)
                ->placeholder('e.g. EARLYBIRD20')
                ->afterStateUpdated(fn ($state, $set) => $set('code', strtoupper((string) $state)))
                ->live(onBlur: true),

            Select::make('event_id')
                ->label('Event (leave blank for all events)')
                ->options(Event::query()->orderByDesc('starts_at')->pluck('title', 'id'))
                ->searchable()
                ->nullable()
                ->placeholder('All events'),

            Select::make('discount_type')
                ->label('Discount type')
                ->options(['percentage' => 'Percentage (%)', 'flat' => 'Flat amount (UGX)'])
                ->required()
                ->default('percentage')
                ->live(),

            TextInput::make('discount_value')
                ->label(fn ($get) => $get('discount_type') === 'flat' ? 'Discount amount (UGX)' : 'Discount (%)')
                ->required()
                ->numeric()
                ->minValue(0)
                ->maxValue(fn ($get) => $get('discount_type') === 'percentage' ? 100 : 99999999),

            TextInput::make('max_discount_amount')
                ->label('Max discount (UGX) — caps percentage discounts')
                ->numeric()
                ->nullable()
                ->placeholder('No cap')
                ->visible(fn ($get) => $get('discount_type') === 'percentage'),

            TextInput::make('min_order_amount')
                ->label('Minimum order (UGX)')
                ->numeric()
                ->nullable()
                ->placeholder('No minimum'),

            TextInput::make('max_uses')
                ->label('Max uses (leave blank for unlimited)')
                ->integer()
                ->nullable()
                ->minValue(1),

            DateTimePicker::make('expires_at')
                ->label('Expires at')
                ->nullable()
                ->placeholder('Never'),

            Toggle::make('is_active')
                ->label('Active')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),

                TextColumn::make('event.title')
                    ->label('Event')
                    ->default('All events')
                    ->limit(30),

                TextColumn::make('discount_type')
                    ->label('Type')
                    ->formatStateUsing(fn ($state) => $state === 'percentage' ? 'Percentage' : 'Flat'),

                TextColumn::make('discount_value')
                    ->label('Value')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->discount_type === 'percentage'
                            ? number_format((float) $state, 0) . '%'
                            : 'UGX ' . number_format((float) $state, 0)
                    ),

                TextColumn::make('uses')
                    ->label('Uses')
                    ->formatStateUsing(fn ($state, $record) =>
                        $state . ($record->max_uses ? ' / ' . $record->max_uses : '')
                    ),

                TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y H:i')
                    ->default('Never'),

                ToggleColumn::make('is_active')->label('Active'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPromoCodes::route('/'),
            'create' => CreatePromoCode::route('/create'),
            'edit'   => EditPromoCode::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['code'] = strtoupper(trim((string) ($data['code'] ?? '')));
        $data['created_by_user_id'] = auth()->id();
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['code'] = strtoupper(trim((string) ($data['code'] ?? '')));
        return $data;
    }
}
