<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\HomepageSettings;
use BackedEnum;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

class HomepageSettingsPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Homepage';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 25;

    protected static ?string $title = 'Homepage Settings';

    protected string $view = 'filament.pages.homepage-settings-page';

    public array $data = [];

    // Snapshot counts computed at mount time
    public int $featuredNow   = 0;
    public int $trendingNow   = 0;
    public int $upcomingNow   = 0;

    public function mount(): void
    {
        $settings = HomepageSettings::current();

        $this->data = [
            'featured_count' => $settings->featured_count,
            'trending_count' => $settings->trending_count,
            'upcoming_count' => $settings->upcoming_count,
            'trending_days'  => $settings->trending_days,
            'show_featured'  => $settings->show_featured,
            'show_trending'  => $settings->show_trending,
            'show_upcoming'  => $settings->show_upcoming,
            'empty_heading'  => $settings->empty_heading,
            'empty_sub'      => $settings->empty_sub,
        ];

        // Live snapshot counts
        $today = now()->startOfDay();

        $this->featuredNow = Event::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->where('starts_at', '>=', $today)
            ->count();

        $featuredIds = Event::query()
            ->where('status', 'published')
            ->where('is_featured', true)
            ->where('starts_at', '>=', $today)
            ->pluck('id');

        $this->trendingNow = Event::query()
            ->where('status', 'published')
            ->where('starts_at', '>=', $today)
            ->whereNotIn('id', $featuredIds)
            ->withCount(['paymentTransactions as sales_count' => fn ($q) =>
                $q->where('status', 'CONFIRMED')
                  ->where('created_at', '>=', now()->subDays($settings->trending_days))
            ])
            ->having('sales_count', '>', 0)
            ->count();

        $excludedIds = $featuredIds->merge(
            Event::query()
                ->where('status', 'published')
                ->where('starts_at', '>=', $today)
                ->whereNotIn('id', $featuredIds)
                ->withCount(['paymentTransactions as sales_count' => fn ($q) =>
                    $q->where('status', 'CONFIRMED')
                      ->where('created_at', '>=', now()->subDays($settings->trending_days))
                ])
                ->having('sales_count', '>', 0)
                ->pluck('id')
        );

        $this->upcomingNow = Event::query()
            ->where('status', 'published')
            ->where('starts_at', '>=', $today)
            ->whereNotIn('id', $excludedIds)
            ->count();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Sections')
                    ->description('Control which sections appear on the homepage and how many events each shows.')
                    ->schema([
                        Grid::make(3)->schema([
                            // Featured
                            Section::make('Featured')
                                ->schema([
                                    Toggle::make('show_featured')
                                        ->label('Show Featured section')
                                        ->onColor('primary'),
                                    TextInput::make('featured_count')
                                        ->label('Max events shown')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(20)
                                        ->required(),
                                ]),

                            // Trending
                            Section::make('Trending')
                                ->schema([
                                    Toggle::make('show_trending')
                                        ->label('Show Trending section')
                                        ->onColor('primary'),
                                    TextInput::make('trending_count')
                                        ->label('Max events shown')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(20)
                                        ->required(),
                                    TextInput::make('trending_days')
                                        ->label('Lookback window (days)')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(365)
                                        ->required()
                                        ->helperText('Sales within this many days count toward trending rank.'),
                                ]),

                            // Upcoming
                            Section::make('Upcoming')
                                ->schema([
                                    Toggle::make('show_upcoming')
                                        ->label('Show Upcoming section')
                                        ->onColor('primary'),
                                    TextInput::make('upcoming_count')
                                        ->label('Max events shown')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(40)
                                        ->required(),
                                ]),
                        ]),
                    ]),

                Section::make('Empty state')
                    ->description('Shown when no published events are available across all sections.')
                    ->schema([
                        TextInput::make('empty_heading')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120),
                        Textarea::make('empty_sub')
                            ->label('Subheading')
                            ->rows(2)
                            ->maxLength(300),
                    ]),

                Section::make('Live snapshot')
                    ->description('Current counts in the database at the time this page was loaded.')
                    ->schema([
                        Grid::make(3)->schema([
                            Placeholder::make('featured_snapshot')
                                ->label('Featured now')
                                ->content(fn () => $this->featuredNow . ' published event(s) pinned and starting today or later'),

                            Placeholder::make('trending_snapshot')
                                ->label('Trending now')
                                ->content(fn () => $this->trendingNow . ' event(s) with confirmed sales in the lookback window'),

                            Placeholder::make('upcoming_snapshot')
                                ->label('Upcoming now')
                                ->content(fn () => $this->upcomingNow . ' published event(s) not in Featured or Trending'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = HomepageSettings::current();
        $settings->update($data);

        // Flush homepage caches
        Cache::forget('home:featured');
        Cache::forget('home:upcoming');
        Cache::forget('home:all_fallback');
        Cache::forget('home:category_pills');

        // Flush all possible trending window keys (current + common values)
        foreach ([7, 14, 30, 60, 90, (int) $data['trending_days']] as $days) {
            Cache::forget('home:trending_' . $days);
        }

        Notification::make()
            ->title('Homepage settings saved.')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }
}
