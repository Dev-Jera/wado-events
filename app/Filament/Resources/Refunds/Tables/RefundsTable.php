<?php

namespace App\Filament\Resources\Refunds\Tables;

use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentLifecycleService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RefundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->description(fn (PaymentTransaction $record): string => (string) ($record->user?->email ?? ''))
                    ->searchable(query: function ($query, string $search): void {
                        $query->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    }),
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable(query: function ($query, string $search): void {
                        $query->orWhereHas('event', function ($eventQuery) use ($search): void {
                            $eventQuery->where('title', 'like', "%{$search}%");
                        });
                    })
                    ->wrap(),
                TextColumn::make('quantity')
                    ->label('Tickets')
                    ->numeric(),
                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state): string => 'UGX ' . number_format((float) $state, 0)),
                TextColumn::make('ticket.used_at')
                    ->label('Scan Status')
                    ->formatStateUsing(fn ($state): string => $state ? 'Scanned' : 'Not scanned')
                    ->badge()
                    ->color(fn ($state): string => $state ? 'danger' : 'success'),
                TextColumn::make('last_error')
                    ->label('Reason / Issue')
                    ->formatStateUsing(fn ($state, PaymentTransaction $record): string => (string) ($record->refund_request_reason ?: $state ?: '—'))
                    ->wrap()
                    ->limit(110)
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('refund_request_status')
                    ->label('Request Status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => strtoupper((string) ($state ?: '—')))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('refund_requested_at')
                    ->label('Requested At')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('refunded_at')
                    ->label('Refunded At')
                    ->dateTime()
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('refund')
                    ->label('Refund')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Refund payment')
                    ->modalDescription('This will mark the transaction as REFUNDED, cancel its ticket, and return inventory when applicable.')
                    ->form([
                        Textarea::make('reason')
                            ->label('Refund reason')
                            ->required()
                            ->maxLength(500),
                    ])
                    ->visible(fn (PaymentTransaction $record): bool => in_array($record->status, [
                        PaymentTransaction::STATUS_CONFIRMED,
                        PaymentTransaction::STATUS_PENDING,
                        PaymentTransaction::STATUS_INITIATED,
                    ], true) && (bool) (auth()->user()?->isAdmin() || auth()->user()?->isSuperAdmin()))
                    ->action(function (PaymentTransaction $record, array $data): void {
                        $record->loadMissing('ticket');

                        if ($record->ticket?->used_at) {
                            Notification::make()
                                ->title('Refund blocked for scanned ticket')
                                ->body('This ticket has already been scanned at the gate. Refund was not applied.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $reason = trim((string) ($data['reason'] ?? ''));
                        $actor = auth()->user()?->name ?: 'admin';

                        $result = app(PaymentLifecycleService::class)->refundWithProvider(
                            $record->fresh(['ticket']),
                            "Manual refund by {$actor}. Reason: {$reason}"
                        );

                        if (! ($result['ok'] ?? false)) {
                            Notification::make()
                                ->title('Refund failed at MarzPay')
                                ->body((string) ($result['message'] ?? 'Provider rejected refund request.'))
                                ->danger()
                                ->send();

                            return;
                        }

                        Notification::make()
                            ->title('Refund submitted successfully')
                            ->body((string) ($result['message'] ?? 'MarzPay accepted refund request.'))
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
