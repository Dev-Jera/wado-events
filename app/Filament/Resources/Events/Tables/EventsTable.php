<?php

namespace App\Filament\Resources\Events\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Event name + venue below
                TextColumn::make('title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn ($record): string => Str::limit(trim("{$record->venue}, {$record->city}"), 28)),

                // Status badge
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft'     => 'warning',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),

                // Schedule date + time
                TextColumn::make('starts_at')
                    ->label('Schedule')
                    ->date('d M Y')
                    ->description(fn ($record): string => $record->starts_at
                        ? $record->starts_at->format('g:i A')
                        : '')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft'     => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                    ]),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
            ])

            // Clicking a row calls selectEvent() on the ListEvents Livewire component.
            // Action named 'view' with no URL → Filament's ListRecords treats it
            // as the row-click action automatically.
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->action(function ($record, $livewire): void {
                        $livewire->selectEvent($record->id);
                    }),

                EditAction::make()
                    ->url(fn ($record): string => \App\Filament\Resources\Events\EventResource::getUrl('edit', ['record' => $record])),

                \Filament\Actions\DeleteAction::make(),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
