<?php

namespace App\Filament\Resources\GateBatches\Pages;

use App\Filament\Resources\GateBatches\GateBatchResource;
use App\Models\Event;
use Filament\Resources\Pages\CreateRecord;

class CreateGateBatch extends CreateRecord
{
    protected static string $resource = GateBatchResource::class;

    public function getSubheading(): ?string
    {
        return 'Define the batch details, then generate and print the tickets.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();

        // Server-side ownership guard: event owners may only create batches
        // for events they own, even if someone crafted a raw POST request.
        if ($user?->isEventOwner() && ! $user->isAdmin()) {
            $event = Event::find($data['event_id'] ?? null);
            abort_unless($event && (int) $event->user_id === (int) $user->id, 403);
        }

        $data['created_by'] = auth()->id();
        $data['status']     = 'draft';

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
