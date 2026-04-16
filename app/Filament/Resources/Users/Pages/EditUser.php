<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $profileImagePath = (string) ($data['profile_image_path'] ?? '');
        $allowedExtensions = ['jpg', 'jpeg', 'jfif', 'png', 'webp'];

        if ($profileImagePath !== '') {
            $extension = Str::lower(pathinfo($profileImagePath, PATHINFO_EXTENSION));
            $isSupported = in_array($extension, $allowedExtensions, true);
            $existsOnDisk = Storage::disk('public')->exists($profileImagePath);

            if (! $isSupported || ! $existsOnDisk) {
                // Avoid endless preview loading for legacy or missing files.
                $data['profile_image_path'] = null;
            }
        }

        return $data;
    }

    protected function afterSave(): void
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
