<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class GatePortalPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Gate Portal';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.pages.gate-portal-page';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isGateStaff();
    }
}
