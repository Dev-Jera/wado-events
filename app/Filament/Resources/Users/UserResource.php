<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Users & Agents';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'Users & Agents';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 15;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            FileUpload::make('profile_image_path')
                ->label('Profile image')
                ->image()
                ->disk('public')
                ->directory('users/profile-images')
                ->visibility('public')
                ->nullable(),

            TextInput::make('name')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            TextInput::make('phone')
                ->maxLength(30)
                ->nullable(),

            Select::make('role')
                ->required()
                ->options(fn (): array => static::getAssignableRoles()),

            TextInput::make('password')
                ->password()
                ->revealable()
                ->minLength(8)
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('profile_image_path')
                    ->label('Image')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?name=' . urlencode((string) $record->name) . '&background=1f66d5&color=ffffff'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->isSuperAdmin() || $user?->isAdmin());
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
        return false;
    }

    protected static function getAssignableRoles(): array
    {
        $roles = [
            'customer' => 'CUSTOMER',
            'agent' => 'AGENT',
            'gate' => 'GATE',
            'gate_agent' => 'GATE_AGENT',
            'verification_officer' => 'VERIFICATION_OFFICER',
            'event_owner' => 'EVENT_OWNER',
        ];

        if (auth()->user()?->isSuperAdmin()) {
            $roles['admin'] = 'ADMIN';
        }

        return $roles;
    }
}
