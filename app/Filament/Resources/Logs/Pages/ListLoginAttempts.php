<?php

namespace App\Filament\Resources\Logs\Pages;

use App\Filament\Resources\Logs\LoginAttemptResource;
use Filament\Resources\Pages\ListRecords;

class ListLoginAttempts extends ListRecords
{
    protected static string $resource = LoginAttemptResource::class;
}
