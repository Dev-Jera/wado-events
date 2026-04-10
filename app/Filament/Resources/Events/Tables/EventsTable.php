<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record): string => trim("{$record->venue}, {$record->city}")),
                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable()
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Starts')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ticket_price')
                    ->label('From')
                    ->formatStateUsing(fn ($state): string => ((float) $state) <= 0 ? 'Free' : 'UGX ' . number_format((float) $state)),
                TextColumn::make('capacity')
                    ->sortable(),
                TextColumn::make('tickets_available')
                    ->label('Available')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])
            ->recordUrl(fn ($record): string => route('admin.events.show', $record))
            ->recordActions([
                ViewAction::make('details')
                    ->label('Details')
                    ->url(fn ($record): string => route('admin.events.show', $record)),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
