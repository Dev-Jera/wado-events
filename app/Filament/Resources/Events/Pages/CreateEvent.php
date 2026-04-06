<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

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
        $data['image_url'] = self::normalizeImagePath($data['image_url'] ?? null);

        unset($data['artists'], $data['ticketCategories'], $data['is_free']);

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        return static::getResource()::getModel()::create($data);
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
