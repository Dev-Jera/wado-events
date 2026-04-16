<?php

namespace App\Filament\Resources\Refunds\Tables;

use App\Models\PaymentTransaction;
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
            ]);
    }
}
