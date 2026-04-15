<?php

namespace App\Filament\Resources\PaymentTransactions\Tables;

use App\Jobs\IssueTicketForPayment;
use App\Models\PaymentTransaction;
use App\Services\Payment\PaymentNotificationService;
use Filament\Actions\Action;
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
            ]);
    }
}
