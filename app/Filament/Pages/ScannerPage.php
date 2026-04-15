<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class ScannerPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Scanner';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.scanner-page';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isGateStaff();
    }
}
