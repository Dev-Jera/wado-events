<?php

namespace App\Filament\Resources\PaymentTransactions\Pages;

use App\Filament\Resources\PaymentTransactions\PaymentTransactionResource;
use App\Models\PaymentTransaction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewPaymentTransaction extends ViewRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Status & Amounts')
                ->columns(3)
                ->schema([
                    TextEntry::make('id')->label('Payment ID'),
                    TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            PaymentTransaction::STATUS_CONFIRMED => 'success',
                            PaymentTransaction::STATUS_PENDING, PaymentTransaction::STATUS_INITIATED => 'warning',
                            PaymentTransaction::STATUS_REFUNDED => 'info',
                            default => 'danger',
                        }),
                    TextEntry::make('payment_provider')->label('Provider'),
                    TextEntry::make('unit_price')->label('Unit Price')
                        ->formatStateUsing(fn ($state) => 'UGX ' . number_format((float) $state, 0)),
                    TextEntry::make('discount_amount')->label('Discount')
                        ->formatStateUsing(fn ($state) => $state > 0 ? '− UGX ' . number_format((float) $state, 0) : '—'),
                    TextEntry::make('total_amount')->label('Total Charged')
                        ->formatStateUsing(fn ($state) => 'UGX ' . number_format((float) $state, 0)),
                ]),

            Section::make('Buyer & Contact')
                ->columns(3)
                ->schema([
                    TextEntry::make('holder_name')->label('Holder Name'),
                    TextEntry::make('phone_number')->label('Phone'),
                    TextEntry::make('quantity')->label('Qty'),
                    TextEntry::make('user.name')->label('Account'),
                    TextEntry::make('user.email')->label('Email'),
                    TextEntry::make('event.title')->label('Event'),
                ]),

            Section::make('Provider Details')
                ->columns(2)
                ->schema([
                    TextEntry::make('idempotency_key')->label('Idempotency Key')->copyable(),
                    TextEntry::make('provider_reference')->label('Provider Reference')->copyable()->placeholder('—'),
                    TextEntry::make('provider_status')->label('Provider Status')->placeholder('—'),
                    TextEntry::make('currency')->label('Currency'),
                ]),

            Section::make('Error Details')
                ->collapsible()
                ->schema([
                    TextEntry::make('last_error')
                        ->label('Last Error (full text)')
                        ->placeholder('No error recorded.')
                        ->columnSpanFull()
                        ->copyable(),
                ]),

            Section::make('Provider Payload (JSON)')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('provider_payload')
                        ->label('Initiation Response')
                        ->placeholder('—')
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—')
                        ->fontFamily('mono'),
                ]),

            Section::make('Webhook Payload (JSON)')
                ->collapsible()
                ->collapsed()
                ->schema([
                    TextEntry::make('webhook_payload')
                        ->label('Callback Received')
                        ->placeholder('—')
                        ->columnSpanFull()
                        ->formatStateUsing(fn ($state) => $state ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '—')
                        ->fontFamily('mono'),
                ]),

            Section::make('Timeline')
                ->columns(3)
                ->schema([
                    TextEntry::make('created_at')->label('Initiated')->dateTime('d M Y H:i:s'),
                    TextEntry::make('confirmed_at')->label('Confirmed')->dateTime('d M Y H:i:s')->placeholder('—'),
                    TextEntry::make('failed_at')->label('Failed')->dateTime('d M Y H:i:s')->placeholder('—'),
                    TextEntry::make('callback_received_at')->label('Webhook Received')->dateTime('d M Y H:i:s')->placeholder('—'),
                    TextEntry::make('expires_at')->label('Expires')->dateTime('d M Y H:i:s')->placeholder('—'),
                    TextEntry::make('refunded_at')->label('Refunded')->dateTime('d M Y H:i:s')->placeholder('—'),
                ]),

        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('← Back to list')
                ->url(PaymentTransactionResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
