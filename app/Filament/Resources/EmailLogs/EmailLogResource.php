<?php

namespace App\Filament\Resources\EmailLogs;

use App\Models\EmailLog;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Email Logs';

    protected static ?string $modelLabel = 'Email Log';

    protected static ?string $pluralModelLabel = 'Email Logs';

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
                    ->label('Sent at')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('recipient')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('ticket.ticket_code')
                    ->label('Ticket')
                    ->searchable()
                    ->default('—'),

                TextColumn::make('ticket.event.title')
                    ->label('Event')
                    ->limit(30)
                    ->default('—'),

                TextColumn::make('subject')
                    ->limit(40)
                    ->toggleable(),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'sent' ? 'success' : 'danger'),

                TextColumn::make('error')
                    ->label('Error')
                    ->limit(50)
                    ->default('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailLogs::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin();
    }
}
