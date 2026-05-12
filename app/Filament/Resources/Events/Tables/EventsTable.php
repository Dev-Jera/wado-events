<?php

namespace App\Filament\Resources\Events\Tables;

use App\Filament\Resources\Events\EventResource;
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

                TextColumn::make('verification_mode')
                    ->label('Verification')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state === 'self_managed' ? 'Self-managed' : 'WADO-managed')
                    ->color(fn (?string $state): string => $state === 'self_managed' ? 'warning' : 'info'),

                TextColumn::make('service_package')
                    ->label('Mode')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'premium_wristbands' => 'PHYSICAL - Wristbands',
                        'batch_tickets' => 'PHYSICAL - Batch tickets',
                        default => 'ONLINE',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'premium_wristbands' => 'danger',
                        'batch_tickets' => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('fulfilment_status')
                    ->label('Fulfilment')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'pending' => 'Pending prep',
                        'in_progress' => 'In progress',
                        'ready' => 'Ready',
                        'delivered' => 'Delivered',
                        default => 'Not required',
                    })
                    ->color(fn (?string $state): string => match ($state) {
                        'pending' => 'warning',
                        'in_progress' => 'info',
                        'ready' => 'primary',
                        'delivered' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('access_mode')
                    ->label('Access')
                    ->state(fn (): string => auth()->user()?->isGateStaff() ? 'READ ONLY' : 'EDIT')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'READ ONLY' ? 'gray' : 'success'),
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
                SelectFilter::make('verification_mode')
                    ->label('Verification Mode')
                    ->options([
                        'wado_managed' => 'WADO-managed',
                        'self_managed' => 'Self-managed',
                    ]),
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
                    ->url(fn ($record): string => \App\Filament\Resources\Events\EventResource::getUrl('edit', ['record' => $record]))
                    ->visible(fn ($record): bool => EventResource::canEdit($record)),

                \Filament\Actions\DeleteAction::make()
                    ->visible(fn ($record): bool => EventResource::canDelete($record)),
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => EventResource::canDeleteAny()),
                ])
                    ->visible(fn (): bool => EventResource::canDeleteAny()),
            ]);
    }
}
