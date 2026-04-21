<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Event;
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
use Illuminate\Support\Str;
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
                ->acceptedFileTypes(['image/jpeg', 'image/pjpeg', 'image/jfif', 'image/png', 'image/webp'])
                ->maxSize(4096)
                ->fetchFileInformation(false)
                ->formatStateUsing(function ($state): ?string {
                    if (! is_string($state) || $state === '') {
                        return null;
                    }

                    $extension = Str::lower(pathinfo($state, PATHINFO_EXTENSION));

                    return in_array($extension, ['jpg', 'jpeg', 'jfif', 'png', 'webp'], true) ? $state : null;
                })
                ->helperText('Use JPG, JFIF, PNG, or WEBP up to 4MB.')
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
                ->live()
                ->options(fn (): array => static::getAssignableRoles()),

            Select::make('event_ids')
                ->label('Assigned gate events')
                ->multiple()
                ->searchable()
                ->preload()
                ->helperText('Gate officers will only access the selected events in scanner and gate portal.')
                ->options(fn (): array => Event::query()->orderBy('starts_at')->pluck('title', 'id')->all())
                ->visible(fn ($get): bool => $get('role') === 'gate_agent')
                ->formatStateUsing(fn (?User $record): array => $record?->gateAssignedEvents()->pluck('events.id')->all() ?? [])
                ->dehydrated(false),

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
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'super_admin' => 'SUPER ADMIN',
                            'gate_agent' => 'GATE OFFICER',
                            'customer' => 'CUSTOMER',
                            default => strtoupper($state),
                        };
                    })
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
        return [
            'super_admin' => 'SUPER ADMIN',
            'admin' => 'ADMIN',
            'event_owner' => 'EVENT OWNER',
            'gate_agent' => 'GATE OFFICER',
            'customer' => 'CUSTOMER',
        ];
    }
}
