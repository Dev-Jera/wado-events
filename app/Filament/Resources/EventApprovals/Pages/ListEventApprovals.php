<?php

namespace App\Filament\Resources\EventApprovals\Pages;

use App\Filament\Resources\EventApprovalsResource;
use Filament\Resources\Pages\ListRecords;

class ListEventApprovals extends ListRecords
{
    protected static string $resource = EventApprovalsResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
