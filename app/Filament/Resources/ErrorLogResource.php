<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ErrorLogResource\Pages;
use App\Models\ErrorLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class ErrorLogResource extends Resource
{
    protected static ?string $model = ErrorLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedExclamationTriangle;

    protected static ?string $navigationLabel = 'Error Logs';

    protected static ?int $navigationSort = 52;

    protected static string|UnitEnum|null $navigationGroup = 'Logs';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, H:i:s')
                    ->sortable()
                    ->searchable(),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'danger' => 'webhook',
                        'warning' => 'payment',
                        'info' => 'ticket',
                        'secondary' => 'queue',
                    ])
                    ->sortable(),

                BadgeColumn::make('severity')
                    ->label('Severity')
                    ->colors([
                        'danger' => 'error',
                        'warning' => 'warning',
                        'success' => 'info',
                    ])
                    ->sortable(),

                TextColumn::make('message')
                    ->label('Issue')
                    ->wrap()
                    ->limit(80)
                    ->searchable(),

                TextColumn::make('user_id')
                    ->label('User')
                    ->searchable(),

                TextColumn::make('event_id')
                    ->label('Event')
                    ->searchable(),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListErrorLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin();
    }
}
