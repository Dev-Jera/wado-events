<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top:1.5rem;">
            <x-filament::button type="submit" size="lg"
                style="background:#0a4fbe !important; border-color:#0a4fbe !important; color:#fff !important; border-radius:10px !important; font-weight:700 !important; min-height:44px !important; padding-inline:1.5rem !important;">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    <x-filament-actions::modals />
</x-filament-panels::page>
