<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected string $view = 'filament.resources.events.create-event';

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        return 'Fill in the details below to list your event on the platform.';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $ticketCategories = collect($data['ticketCategories'] ?? []);
        $isFree = (bool) ($data['is_free'] ?? false);

        if ($isFree) {
            $ticketCategories = $ticketCategories->map(function (array $ticketCategory): array {
                $ticketCategory['price'] = 0;

                return $ticketCategory;
            });
        }

        $data['slug'] = Str::slug($data['title']) . '-' . Str::lower(Str::random(6));
        $data['user_id'] = Auth::id();
        $data['capacity'] = (int) $ticketCategories->sum('ticket_count');
        $data['tickets_available'] = (int) $ticketCategories->sum('ticket_count');
        $data['ticket_price'] = (float) ($ticketCategories->min('price') ?? 0);
        $data['is_free'] = $isFree;
        $data['image_url'] = self::normalizeImagePath($data['image_url'] ?? null);

        unset($data['artists'], $data['ticketCategories']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $event = $this->getRecord();

        if ($event && (bool) $event->is_free) {
            $event->ticketCategories()->update(['price' => 0]);
        }
    }

    protected static function normalizeImagePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['/images/', '/storage/'])) {
            return $path;
        }

        return '/storage/' . ltrim($path, '/');
    }
}
