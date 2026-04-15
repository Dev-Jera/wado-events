<?php

namespace App\Filament\Resources\PaymentTransactions\Tables;

use App\Jobs\IssueTicketForPayment;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentLifecycleService;
use App\Services\Payment\PaymentNotificationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentTransactionsTable
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
                    ->searchable(query: function ($query, string $search) {
                        $query->orWhereHas('user', function ($userQuery) use ($search): void {
                            $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                    })
                    ->description(fn (PaymentTransaction $record): string => (string) ($record->user?->email ?? '')),
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable(query: function ($query, string $search) {
                        $query->orWhereHas('event', function ($eventQuery) use ($search): void {
                            $eventQuery->where('title', 'like', "%{$search}%");
                        });
                    })
                    ->wrap(),
                TextColumn::make('quantity')
                    ->label('Tickets')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Amount')
                    ->formatStateUsing(fn ($state): string => 'UGX ' . number_format((float) $state, 0)),
                TextColumn::make('payment_provider')
                    ->label('Provider')
                    ->formatStateUsing(fn ($state): string => strtoupper((string) $state)),
                TextColumn::make('phone_number')
                    ->label('Phone')
                    ->toggleable(),
                TextColumn::make('idempotency_key')
                    ->label('Idempotency Key')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('provider_reference')
                    ->label('Provider Ref')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('last_error')
                    ->label('Reason / Issue')
                    ->wrap()
                    ->limit(90)
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('ticket_id')
                    ->label('Ticket')
                    ->formatStateUsing(fn ($state): string => $state ? (string) $state : 'Pending'),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->options([
                        PaymentTransaction::STATUS_INITIATED => PaymentTransaction::STATUS_INITIATED,
                        PaymentTransaction::STATUS_PENDING => PaymentTransaction::STATUS_PENDING,
                        PaymentTransaction::STATUS_CONFIRMED => PaymentTransaction::STATUS_CONFIRMED,
                        PaymentTransaction::STATUS_FAILED => PaymentTransaction::STATUS_FAILED,
                        PaymentTransaction::STATUS_REFUNDED => PaymentTransaction::STATUS_REFUNDED,
                    ]),
            ])
            ->recordActions([
                Action::make('resend')
                    ->label('Resend')
                    ->tooltip('Use this after a confirmed payment if the attendee did not receive their ticket. It retries ticket issuance or re-sends the confirmation message.')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->visible(fn (PaymentTransaction $record): bool => $record->status === PaymentTransaction::STATUS_CONFIRMED)
                    ->action(function (PaymentTransaction $record): void {
                        if (! $record->ticket_id) {
                            IssueTicketForPayment::dispatch($record->id);

                            Notification::make()
                                ->title('Ticket issuance retry queued.')
                                ->success()
                                ->send();

                            return;
                        }

                        $record->loadMissing('ticket.user');
                        app(PaymentNotificationService::class)->sendTicketConfirmed($record->ticket, $record);

                        Notification::make()
                            ->title('Ticket email/SMS resend attempted.')
                            ->success()
                            ->send();
                    }),

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
