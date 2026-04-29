<?php

namespace App\Filament\Resources\Categories\Tables;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('description')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('events_count')
                    ->label('Events')
                    ->sortable(),
                TextColumn::make('access_mode')
                    ->label('Access')
                    ->state(fn (): string => auth()->user()?->isGateAgent() ? 'READ ONLY' : 'EDIT')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'READ ONLY' ? 'gray' : 'success'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->visible(fn ($record): bool => CategoryResource::canEdit($record)),
                DeleteAction::make()
                    ->visible(fn ($record): bool => CategoryResource::canDelete($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => CategoryResource::canDeleteAny()),
                ])
                    ->visible(fn (): bool => CategoryResource::canDeleteAny()),
            ]);
    }
}
