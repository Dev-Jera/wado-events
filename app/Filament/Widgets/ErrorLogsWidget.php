<?php
namespace App\Filament\Widgets;

use App\Models\ErrorLog;
use Filament\Actions\Action as TableAction;
use Filament\Actions\ViewAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ErrorLogsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isEventOwner();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ErrorLog::query()->latest())
            ->defaultPaginationPageOption(10)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Time')
                    ->dateTime('M d, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'webhook' => 'danger',
                        'payment' => 'warning',
                        'ticket' => 'info',
                        'queue' => 'gray',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('severity')
                    ->label('Severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'error' => 'danger',
                        'warning' => 'warning',
                        'info' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Issue')
                    ->wrap()
                    ->limit(60),

                Tables\Columns\IconColumn::make('resolved_at')
                    ->label('Resolved')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'webhook' => 'Webhook Errors',
                        'payment' => 'Payment Issues',
                        'ticket' => 'Ticket Issues',
                        'queue' => 'Queue Problems',
                    ]),

                Tables\Filters\SelectFilter::make('severity')
                    ->options([
                        'error' => 'Errors',
                        'warning' => 'Warnings',
                        'info' => 'Info',
                    ]),

                Tables\Filters\TernaryFilter::make('resolved_at')
                    ->label('Status')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('resolved_at'),
                        false: fn (Builder $query) => $query->whereNull('resolved_at'),
                    ),
            ])
            ->actions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->url(fn (ErrorLog $record) => route('filament.admin.resources.error-logs.view', $record)),

                TableAction::make('resolve')
                    ->icon('heroicon-o-check')
                    ->hidden(fn (ErrorLog $record) => $record->resolved_at !== null)
                    ->action(fn (ErrorLog $record) => $record->resolve())
                    ->requiresConfirmation()
                    ->color('success'),
            ])
            ->striped();
    }
}