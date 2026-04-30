<?php

namespace App\Filament\Resources\Logs;

use App\Filament\Resources\Logs\Pages\ListLoginAttempts;
use App\Filament\Resources\Logs\Tables\LoginAttemptsTable;
use App\Models\LoginAttempt;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use UnitEnum;

class LoginAttemptResource extends Resource
{
    protected static ?string $model = LoginAttempt::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Login Logs';

    protected static ?string $modelLabel = 'Login Attempt';

    protected static ?string $pluralModelLabel = 'Login Logs';

    protected static string|UnitEnum|null $navigationGroup = 'Logs';

    protected static ?int $navigationSort = 50;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return LoginAttemptsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoginAttempts::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }
}
