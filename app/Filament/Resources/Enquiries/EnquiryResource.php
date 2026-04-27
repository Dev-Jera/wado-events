<?php

namespace App\Filament\Resources\Enquiries;

use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Mail\PackageEnquiryReply;
use App\Models\Enquiry;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use UnitEnum;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'Enquiries';

    protected static ?string $modelLabel = 'Enquiry';

    protected static ?string $pluralModelLabel = 'Enquiries';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 55;

    public static function getNavigationBadge(): ?string
    {
        $count = Enquiry::where('is_read', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('From')
                    ->description(fn (Enquiry $record): string => $record->email)
                    ->searchable(),

                TextColumn::make('package')
                    ->badge()
                    ->color('warning')
                    ->searchable(),

                TextColumn::make('event_date')
                    ->label('Event Date')
                    ->date('d M Y')
                    ->placeholder('—'),

                TextColumn::make('attendance')
                    ->label('Attendance')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(fn (Enquiry $record): string => $record->status)
                    ->color(fn (string $state): string => match ($state) {
                        'New'     => 'info',
                        'Read'    => 'gray',
                        'Replied' => 'success',
                        default   => 'gray',
                    }),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon(Heroicon::OutlinedEye)
                    ->color('gray')
                    ->modalContent(fn (Enquiry $record) => view(
                        'filament.modals.enquiry-detail',
                        ['enquiry' => $record]
                    ))
                    ->action(fn (Enquiry $record) => $record->markAsRead())
                    ->modalSubmitActionLabel('Mark as Read')
                    ->modalCancelActionLabel('Close')
                    ->successNotification(null),

                Action::make('reply')
                    ->label('Reply')
                    ->icon(Heroicon::OutlinedPaperAirplane)
                    ->color('info')
                    ->modalHeading(fn (Enquiry $record) => 'Reply to ' . $record->name)
                    ->modalDescription(fn (Enquiry $record) => 'Will be sent to: ' . $record->email)
                    ->form([
                        Textarea::make('reply_message')
                            ->label('Your reply')
                            ->required()
                            ->rows(7)
                            ->placeholder('Write your message here…'),
                    ])
                    ->modalSubmitActionLabel('Send Reply')
                    ->action(function (Enquiry $record, array $data): void {
                        Mail::to($record->email)->send(
                            new PackageEnquiryReply($record, $data['reply_message'])
                        );
                        $record->update(['replied_at' => now(), 'is_read' => true]);
                        Notification::make()
                            ->title('Reply sent to ' . $record->email)
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEnquiries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }
}
