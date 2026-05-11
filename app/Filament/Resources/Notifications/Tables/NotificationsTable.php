<?php

namespace App\Filament\Resources\Notifications\Tables;

use App\Models\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                BadgeColumn::make('read_status')
                    ->label('Status')
                    ->getStateUsing(fn (Notification $record) => $record->isRead() ? 'Read' : 'Unread')
                    ->color(fn (string $state): string => $state === 'Read' ? 'gray' : 'warning'),

                TextColumn::make('data.title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('data.body')
                    ->label('Message')
                    ->limit(60)
                    ->wrap()
                    ->searchable(),

                BadgeColumn::make('type')
                    ->label('Type')
                    ->getStateUsing(fn (Notification $record) => $record->getCategory())
                    ->color(fn (Notification $record): string => $record->getColorBadge()),

                TextColumn::make('created_at')
                    ->label('Received at')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('unread')
                    ->label('Unread only')
                    ->query(fn (Builder $query) => $query->whereNull('read_at')),

                Filter::make('read')
                    ->label('Read only')
                    ->query(fn (Builder $query) => $query->whereNotNull('read_at')),
            ]);
    }
}
