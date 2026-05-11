<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('mark_all_read')
                ->label('Mark all as read')
                ->requiresConfirmation()
                ->color('gray')
                ->action(function () {
                    $user = auth()->user();
                    $user->notifications()->whereNull('read_at')->update(['read_at' => now()]);
                    $this->dispatch('refresh');
                }),
        ];
    }
}
