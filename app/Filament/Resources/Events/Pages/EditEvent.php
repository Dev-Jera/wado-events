<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $ticketPrices = $this->getRecord()->ticketCategories->pluck('price');

        $data['is_free'] = $ticketPrices->isNotEmpty() && $ticketPrices->every(fn ($price): bool => (float) $price <= 0);
        $data['image_url'] = $this->normalizeImageState($data['image_url'] ?? null);

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $ticketCategories = collect($data['ticketCategories'] ?? []);
        $isFree = (bool) ($data['is_free'] ?? false);

        if ($isFree) {
            $ticketCategories = $ticketCategories->map(function (array $ticketCategory): array {
                $ticketCategory['price'] = 0;

                return $ticketCategory;
            });
        }

        $data['capacity'] = (int) $ticketCategories->sum('ticket_count');
        $data['tickets_available'] = (int) $ticketCategories->sum('ticket_count');
        $data['ticket_price'] = (float) ($ticketCategories->min('price') ?? 0);
        $data['image_url'] = self::normalizeImagePath($data['image_url'] ?? null);

        unset($data['artists'], $data['ticketCategories'], $data['is_free']);

        return $data;
    }

    protected function normalizeImageState(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, '/storage/')) {
            return ltrim(Str::after($path, '/storage/'), '/');
        }

        return $path;
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
