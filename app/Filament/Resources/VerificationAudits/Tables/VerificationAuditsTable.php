<?php

namespace App\Filament\Resources\VerificationAudits\Tables;

use App\Models\Ticket;
use App\Services\Ticket\TicketQrService;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class VerificationAuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('purchased_at', 'desc')
            ->columns([
                TextColumn::make('ticket_code')
                    ->label('Ticket Code')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('holder_name')
                    ->label('Name')
                    ->formatStateUsing(fn ($state, Ticket $record): string => (string) ($state ?: ($record->user?->name ?? 'N/A')))
                    ->searchable(query: function ($query, string $search): void {
                        $query->orWhere('holder_name', 'like', "%{$search}%")
                            ->orWhereHas('user', fn ($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable(query: function ($query, string $search): void {
                        $query->orWhereHas('event', fn ($q) => $q->where('title', 'like', "%{$search}%"));
                    })
                    ->wrap(),
                TextColumn::make('status')
                    ->label('Ticket State')
                    ->formatStateUsing(fn ($state, Ticket $record): string => ($record->used_at || $state === 'used') ? 'SCANNED_USED' : 'WAITING_FOR_SCAN')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'SCANNED_USED' ? 'warning' : 'gray'),
                TextColumn::make('paymentTransaction.status')
                    ->label('Payment')
                    ->formatStateUsing(fn ($state): string => strtoupper((string) ($state ?: 'N/A')))
                    ->badge()
                    ->color(fn (string $state): string => $state === 'CONFIRMED' ? 'success' : 'danger'),
                TextColumn::make('allocated_signature')
                    ->label('Allocated Signature')
                    ->state(function (Ticket $record): string {
                        $payload = app(TicketQrService::class)->buildSignedPayload($record);

                        return (string) ($payload['sig'] ?? '');
                    })
                    ->searchable(false)
                    ->copyable()
                    ->limit(18)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()),
                TextColumn::make('latestScanLog.scanned_payload')
                    ->label('Scanned Signature')
                    ->state(function (Ticket $record): string {
                        $payload = app(TicketQrService::class)->parsePayload((string) ($record->latestScanLog?->scanned_payload ?? ''));

                        return (string) ($payload['sig'] ?? 'N/A');
                    })
                    ->copyable()
                    ->limit(18)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState()),
                TextColumn::make('signature_match')
                    ->label('Signature Match')
                    ->state(function (Ticket $record): string {
                        $service = app(TicketQrService::class);
                        $allocated = $service->buildSignedPayload($record);
                        $parsed = $service->parsePayload((string) ($record->latestScanLog?->scanned_payload ?? ''));

                        if (! is_array($parsed)) {
                            return 'N/A';
                        }

                        return hash_equals((string) ($allocated['sig'] ?? ''), (string) ($parsed['sig'] ?? '')) ? 'TRUE' : 'FALSE';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'TRUE' => 'success',
                        'FALSE' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('latestScanLog.result')
                    ->label('Last Scan Result')
                    ->formatStateUsing(fn ($state): string => strtoupper((string) ($state ?: 'NOT_SCANNED')))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'VALID' => 'success',
                        'NOT_SCANNED' => 'gray',
                        default => 'danger',
                    }),
                TextColumn::make('latestScanLog.staff.name')
                    ->label('Scanned By')
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('latestScanLog.scanned_at')
                    ->label('Scanned At')
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('latestScanLog.scanned_payload')
                    ->label('Raw Scanned Payload')
                    ->limit(60)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState())
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('status')
                    ->label('Ticket Status')
                    ->options([
                        'confirmed' => 'CONFIRMED',
                        'used' => 'USED',
                        'cancelled' => 'CANCELLED',
                    ]),
            ]);
    }
}
