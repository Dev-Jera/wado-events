<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected string $view = 'filament.resources.users.list-users';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Create Agent / User')
                ->visible(function (): bool {
                    $user = auth()->user();

                    // Super admin and admin can always create agents
                    if ($user?->isSuperAdmin() || $user?->isAdmin()) {
                        return true;
                    }

                    // Event owners can only create agents if they have self_managed events
                    if ($user?->role === 'event_owner') {
                        return $user->events()->where('verification_mode', 'self_managed')->exists();
                    }

                    return false;
                }),
        ];
    }
}
