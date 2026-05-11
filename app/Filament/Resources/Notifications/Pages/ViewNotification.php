<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use App\Models\Notification;
use Filament\Resources\Pages\Page;
use Filament\Actions;

class ViewNotification extends Page
{
    protected static string $resource = NotificationResource::class;

    public ?Notification $record = null;

    public function mount(string|int $record): void
    {
        $this->record = Notification::findOrFail($record);

        $this->title = $this->record->getTitle();
    }

    public function getView(): string
    {
        return 'filament.resources.notifications.pages.view-notification';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('toggle_read')
                ->label(fn () => $this->record->isRead() ? 'Mark as unread' : 'Mark as read')
                ->icon(fn () => $this->record->isRead() ? 'heroicon-o-envelope-open' : 'heroicon-o-envelope')
                ->color('gray')
                ->action(function () {
                    if ($this->record->isRead()) {
                        $this->record->markAsUnread();
                    } else {
                        $this->record->markAsRead();
                    }
                    $this->record->refresh();
                })
                ->after(fn () => $this->dispatch('refresh')),
        ];
    }
}
