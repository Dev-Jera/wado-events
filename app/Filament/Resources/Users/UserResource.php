<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Event;
use App\Models\User;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
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
        $auth       = auth()->user();
        $isSuperAdmin = $auth?->isSuperAdmin();

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
                ->options(fn (?User $record): array => static::getAssignableRoles($record))
                // Super admins cannot demote themselves — that would lock them out
                ->disabled(fn (?User $record): bool => $record?->id === $auth?->id && $isSuperAdmin)
                ->helperText(fn (?User $record): ?string =>
                    ($record?->id === $auth?->id && $isSuperAdmin)
                        ? 'You cannot change your own role.'
                        : null
                ),

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
                ->label(fn (?User $record): string => $record ? 'New password (leave blank to keep current)' : 'Password')
                ->password()
                ->revealable()
                ->minLength(8)
                ->required(fn (string $operation): bool => $operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
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
                    ->defaultImageUrl(fn (User $record): string =>
                        'https://ui-avatars.com/api/?name=' . urlencode((string) $record->name) . '&background=1f66d5&color=ffffff'
                    ),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('phone')->placeholder('—')->searchable(),
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        'event_owner' => 'info',
                        'gate_agent'  => 'primary',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'event_owner' => 'Event Owner',
                        'gate_agent'  => 'Gate Officer',
                        'customer'    => 'Customer',
                        default       => ucfirst($state),
                    })
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([])
            ->recordActions([
                \Filament\Actions\EditAction::make()
                    ->visible(fn (User $record): bool => static::canEdit($record)),

                DeleteAction::make()
                    ->label('Delete')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string => "Delete {$record->name}?")
                    ->modalDescription('This will permanently remove the user. This cannot be undone.')
                    ->visible(fn (User $record): bool => static::canDelete($record)),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }

    // ── Access control ────────────────────────────────────────────────────────

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->isSuperAdmin() || $user?->isAdmin());
    }

    public static function canCreate(): bool
    {
        $user = auth()->user();
        return (bool) ($user?->isSuperAdmin() || $user?->isAdmin());
    }

    public static function canEdit(Model $record): bool
    {
        $auth = auth()->user();
        if (! $auth) {
            return false;
        }

        // Super admin can edit anyone
        if ($auth->isSuperAdmin()) {
            return true;
        }

        // Admin can only edit roles below themselves (not super_admin or admin)
        if ($auth->isAdmin()) {
            return ! $record->isSuperAdmin() && $record->role !== 'admin';
        }

        return false;
    }

    public static function canDelete(Model $record): bool
    {
        // Super admin accounts are permanently protected — nobody can delete them
        if ($record->isSuperAdmin()) {
            return false;
        }

        // Only super admin can delete other users
        // Also prevent self-deletion
        $auth = auth()->user();
        return $auth?->isSuperAdmin() && $auth->id !== $record->id;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Returns the roles the currently logged-in user is allowed to assign.
     * When editing a super_admin record, their role is shown but disabled in the form.
     */
    protected static function getAssignableRoles(?User $record = null): array
    {
        $auth = auth()->user();

        if ($auth?->isSuperAdmin()) {
            return [
                'super_admin' => 'Super Admin',
                'admin'       => 'Admin',
                'event_owner' => 'Event Owner',
                'gate_agent'  => 'Gate Officer',
                'customer'    => 'Customer',
            ];
        }

        // Admin can only assign roles below themselves
        return [
            'event_owner' => 'Event Owner',
            'gate_agent'  => 'Gate Officer',
            'customer'    => 'Customer',
        ];
    }
}
