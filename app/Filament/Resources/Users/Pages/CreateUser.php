<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function getRedirectUrl(): string
    {
        return UserResource::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $eventIds = collect((array) ($this->form->getRawState()['event_ids'] ?? []))
            ->map(fn ($id): int => (int) $id)
            ->filter()
            ->unique()
            ->values();

        if ($this->record->role !== 'gate_agent') {
            $this->record->gateAssignedEvents()->detach();

            return;
        }

        $this->record->gateAssignedEvents()->sync($eventIds->all());
    }
}
