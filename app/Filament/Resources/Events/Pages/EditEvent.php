<?php

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Models\PaymentTransaction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected string $view = 'filament.resources.events.edit-event';

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->getRecord()->title ?? 'Edit Event';
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        $record = $this->getRecord();

        $parts = array_filter([
            $record->venue,
            $record->city,
            $record->starts_at?->format('M d, Y · g:i A'),
        ]);

        return implode(' · ', $parts) ?: null;
    }

    public function getEventStats(): array
    {
        $record = $this->getRecord();

        if (! $record) {
            return ['capacity' => 0, 'ticketsSold' => 0, 'revenue' => 0];
        }

        $capacity    = (int) $record->ticketCategories->sum('ticket_count');
        $ticketsSold = (int) PaymentTransaction::where('event_id', $record->id)
            ->where('status', 'CONFIRMED')
            ->sum('quantity');
        $revenue     = (float) PaymentTransaction::where('event_id', $record->id)
            ->where('status', 'CONFIRMED')
            ->sum('total_amount');

        return compact('capacity', 'ticketsSold', 'revenue');
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
