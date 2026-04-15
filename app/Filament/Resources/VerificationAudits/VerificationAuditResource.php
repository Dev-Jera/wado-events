<?php

namespace App\Filament\Resources\VerificationAudits;

use App\Filament\Resources\VerificationAudits\Pages\ListVerificationAudits;
use App\Filament\Resources\VerificationAudits\Tables\VerificationAuditsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class VerificationAuditResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Verification Audit';

    protected static ?string $modelLabel = 'Verification Audit';

    protected static ?string $pluralModelLabel = 'Verification Audits';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return VerificationAuditsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'event',
                'user',
                'latestScanLog.staff',
            ]);

        $user = auth()->user();
        if ($user?->isEventOwner()) {
            $query->whereHas('event', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id));
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVerificationAudits::route('/'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->canViewOperationsDashboard() || $user?->isGateStaff());
    }
}
