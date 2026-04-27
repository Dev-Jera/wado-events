<?php

namespace App\Filament\Resources\GateBatches\Pages;

use App\Filament\Resources\GateBatches\GateBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListGateBatches extends ListRecords
{
    protected static string $resource = GateBatchResource::class;

    public function getTitle(): string
    {
        return 'Gate Ticket Batches';
    }

    public function getSubheading(): ?string
    {
        return 'Generate and print physical tickets for gate sales.';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('+ New Batch')
                ->visible(fn (): bool => GateBatchResource::canCreate()),
        ];
    }
}
