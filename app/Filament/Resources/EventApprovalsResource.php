<?php

namespace App\Filament\Resources;

use App\Mail\EventApprovedOwnerNextSteps;
use App\Models\Event;
use App\Models\User;
use App\Filament\Resources\EventApprovals\Pages\ListEventApprovals;
use App\Filament\Resources\EventApprovals\Pages\EditEventApproval;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use BackedEnum;
use UnitEnum;

class EventApprovalsResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    protected static ?string $navigationLabel = 'Event Approvals';

    protected static ?string $modelLabel = 'Event Submission';

    protected static ?string $pluralModelLabel = 'Event Submissions';

    protected static string|UnitEnum|null $navigationGroup = 'Events';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'event-approvals';

    public static function getEloquentQuery(): Builder
    {
        // Only show pending events that were submitted by event owners (have user_id)
        return parent::getEloquentQuery()
            ->where('status', 'pending')
            ->whereNotNull('user_id')
            ->with(['category', 'user', 'ticketCategories'])
            ->latest('created_at');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Event Details')
                    ->schema([
                        Placeholder::make('title')
                            ->label('Event Title')
                            ->content(fn (Model $record): ?string => $record->title),

                        Placeholder::make('category.name')
                            ->label('Category')
                            ->content(fn (Model $record): ?string => $record->category?->name),

                        Placeholder::make('venue')
                            ->label('Venue')
                            ->content(fn (Model $record): ?string => $record->venue),

                        Placeholder::make('city')
                            ->label('City')
                            ->content(fn (Model $record): ?string => $record->city),

                        Placeholder::make('country')
                            ->label('Country')
                            ->content(fn (Model $record): ?string => $record->country),

                        Placeholder::make('starts_at')
                            ->label('Starts At')
                            ->content(fn (Model $record): ?string => $record->starts_at?->format('M d, Y H:i')),

                        Placeholder::make('ends_at')
                            ->label('Ends At')
                            ->content(fn (Model $record): ?string => $record->ends_at?->format('M d, Y H:i')),

                        Placeholder::make('description')
                            ->label('Description')
                            ->content(fn (Model $record): ?string => $record->description)
                            ->columnSpanFull(),

                        Placeholder::make('user.name')
                            ->label('Submitted By')
                            ->content(fn (Model $record): ?string => $record->user?->name . ' (' . $record->user?->email . ')'),

                        Placeholder::make('created_at')
                            ->label('Submitted On')
                            ->content(fn (Model $record): ?string => $record->created_at?->format('M d, Y H:i')),
                    ]),

                Section::make('Approval Decision')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'published' => 'Approve & Publish',
                                'draft' => 'Keep as Draft',
                                'cancelled' => 'Reject',
                            ])
                            ->required()
                            ->default('published'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Event Title')
                    ->sortable()
                    ->searchable()
                    ->limit(40),

                TextColumn::make('user.name')
                    ->label('Submitted By')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                TextColumn::make('starts_at')
                    ->label('Event Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'published' => 'success',
                        'cancelled' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                TextColumn::make('verification_mode')
                    ->label('Verification')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'self_managed'
                        ? 'Self-managed'
                        : 'WADO-managed')
                    ->color(fn (string $state): string => $state === 'self_managed' ? 'warning' : 'info'),

                TextColumn::make('is_featured')
                    ->label('Featured')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Featured' : 'Not Featured')
                    ->color(fn (bool $state): string => $state ? 'warning' : 'gray'),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'published' => 'Published',
                        'draft' => 'Draft',
                        'cancelled' => 'Rejected',
                    ]),
                SelectFilter::make('verification_mode')
                    ->label('Verification Mode')
                    ->options([
                        'wado_managed' => 'WADO-managed',
                        'self_managed' => 'Self-managed',
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->url(fn (Event $record): string => static::getUrl('edit', ['record' => $record])),

                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Event $record): void {
                        if ($record->status !== 'published') {
                            $record->update(['status' => 'published']);
                        }

                        static::notifyEventOwnerOnApproval($record);
                    })
                    ->visible(fn (Event $record) => $record->status === 'pending'),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Event $record) => $record->update(['status' => 'cancelled']))
                    ->visible(fn (Event $record) => $record->status === 'pending'),

                Action::make('feature_home')
                    ->label('Feature')
                    ->icon('heroicon-o-star')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Event $record) => $record->update(['is_featured' => true]))
                    ->visible(fn (Event $record) => ! $record->is_featured),

                Action::make('unfeature_home')
                    ->label('Unfeature')
                    ->icon('heroicon-o-star')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (Event $record) => $record->update(['is_featured' => false]))
                    ->visible(fn (Event $record) => $record->is_featured),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    Action::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                if (! $record instanceof Event) {
                                    continue;
                                }

                                if ($record->status !== 'published') {
                                    $record->update(['status' => 'published']);
                                }

                                static::notifyEventOwnerOnApproval($record);
                            }
                        })
                        ->requiresConfirmation(),

                    Action::make('reject_selected')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn ($records) => $records->each->update(['status' => 'cancelled']))
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->canAccessOperationsPanel();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventApprovals::route('/'),
            'edit' => EditEventApproval::route('/{record}/edit'),
        ];
    }

    public static function notifyEventOwnerOnApproval(Event $event): void
    {
        $event->loadMissing(['user', 'category', 'ticketCategories']);

        $owner = $event->user;

        if (! $owner instanceof User) {
            return;
        }

        if ($owner->role === 'customer') {
            $owner->update(['role' => 'event_owner']);
        }

        $token = Password::broker()->createToken($owner);
        $resetUrl = url(route('password.reset', [
            'token' => $token,
            'email' => $owner->email,
        ], false));

        $dashboardAlias = static::generateDashboardAlias((string) $event->title);
        $dashboardLoginUrl = route('filament.admin.auth.login');

        Mail::to($owner)->send(new EventApprovedOwnerNextSteps(
            event: $event,
            owner: $owner,
            dashboardAlias: $dashboardAlias,
            dashboardLoginUrl: $dashboardLoginUrl,
            setPasswordUrl: $resetUrl,
        ));
    }

    protected static function generateDashboardAlias(string $eventTitle): string
    {
        $slug = Str::slug($eventTitle);

        if ($slug === '') {
            return 'owner-dashboard';
        }

        $parts = array_values(array_filter(explode('-', $slug)));

        if (count($parts) >= 3) {
            $alias = implode('-', array_slice($parts, 0, 3));
        } elseif (count($parts) > 1) {
            $alias = implode('-', $parts);
        } else {
            $alias = $parts[0];
        }

        return Str::limit($alias, 24, '');
    }
}
