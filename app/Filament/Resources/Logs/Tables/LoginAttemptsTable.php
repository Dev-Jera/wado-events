<?php

namespace App\Filament\Resources\Logs\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LoginAttemptsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('attempted_at', 'desc')
            ->columns([
                TextColumn::make('attempted_at')
                    ->label('Date / Time')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),

                IconColumn::make('succeeded')
                    ->label('Result')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('email')
                    ->label('Email Tried')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->copyable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('failure_reason')
                    ->label('Failure Reason')
                    ->placeholder('—')
                    ->badge()
                    ->color('danger'),

                TextColumn::make('user.name')
                    ->label('Logged-in User')
                    ->placeholder('—')
                    ->searchable(query: function (Builder $query, string $search): void {
                        $query->orWhereHas('user', fn (Builder $q) => $q->where('name', 'like', "%{$search}%"));
                    }),

                TextColumn::make('user_agent')
                    ->label('User Agent')
                    ->limit(50)
                    ->tooltip(fn (TextColumn $column): ?string => $column->getState())
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('succeeded')
                    ->label('Result')
                    ->options([
                        '1' => 'Successful logins',
                        '0' => 'Failed attempts',
                    ])
                    ->query(function (Builder $query, array $data): void {
                        if ($data['value'] !== null && $data['value'] !== '') {
                            $query->where('succeeded', (bool) $data['value']);
                        }
                    }),

                Filter::make('today')
                    ->label('Today only')
                    ->query(fn (Builder $query): Builder => $query->whereDate('attempted_at', today())),

                Filter::make('last_hour')
                    ->label('Last 60 minutes')
                    ->query(fn (Builder $query): Builder => $query->where('attempted_at', '>=', now()->subHour())),
            ])
            ->striped()
            ->poll('30s');
    }
}
