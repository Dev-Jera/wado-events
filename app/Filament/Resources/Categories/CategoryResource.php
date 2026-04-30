<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Filament\Resources\Categories\Schemas\CategoryForm;
use App\Filament\Resources\Categories\Tables\CategoriesTable;
use App\Models\Category;
use BackedEnum;
use Filament\Resources\Resource;
use UnitEnum;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Categories';

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

    protected static string|UnitEnum|null $navigationGroup = 'Events';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->isEventOwner()) {
            $query
                ->whereHas('events', fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id))
                ->withCount([
                    'events as events_count' => fn (Builder $eventQuery) => $eventQuery->where('user_id', $user->id),
                ]);

            return $query;
        }

        return $query->withCount('events');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit' => EditCategory::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->canAccessOperationsPanel();
    }

    public static function canCreate(): bool
    {
        return (bool) (auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin());
    }

    public static function canEdit(Model $record): bool
    {
        return (bool) (auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin());
    }

    public static function canDelete(Model $record): bool
    {
        return (bool) (auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin());
    }

    public static function canDeleteAny(): bool
    {
        return (bool) (auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin());
    }
}
