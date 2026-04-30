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
                $data['profile_image_path'] = null;
            }
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $auth   = auth()->user();
        $record = $this->record;

        // Prevent self-demotion: a super admin cannot strip their own role
        if ($record->id === $auth?->id && $record->isSuperAdmin()) {
            $data['role'] = 'super_admin';
        }

        // Server-side guard: admins cannot escalate roles to admin or super_admin
        if (! $auth?->isSuperAdmin()) {
            $allowed = ['event_owner', 'gate_agent', 'customer'];
            if (! in_array($data['role'] ?? '', $allowed, true)) {
                $data['role'] = $record->role; // leave unchanged
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
