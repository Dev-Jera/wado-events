<?php

namespace App\Filament\Resources\EventApprovals\Pages;

use App\Filament\Resources\EventApprovalsResource;
use Filament\Resources\Pages\EditRecord;

class EditEventApproval extends EditRecord
{
    protected static string $resource = EventApprovalsResource::class;

    protected ?string $statusBeforeSave = null;

    protected function beforeSave(): void
    {
        $this->statusBeforeSave = $this->record?->status;
    }

    protected function afterSave(): void
    {
        if (($this->statusBeforeSave !== 'published') && ($this->record?->status === 'published')) {
            EventApprovalsResource::notifyEventOwnerOnApproval($this->record);
        }
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
