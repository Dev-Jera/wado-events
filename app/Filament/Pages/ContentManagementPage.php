<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use UnitEnum;

class ContentManagementPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Manage Home Page';

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 50;

    protected string $view = 'filament.pages.content-management-page';

    public array $data = [];

    protected function getSettingsPath(): string
    {
        return storage_path('app/site-settings.json');
    }

    protected function loadSettings(): array
    {
        $path = $this->getSettingsPath();

        return file_exists($path)
            ? (json_decode(file_get_contents($path), true) ?? [])
            : [];
    }

    protected function saveSettings(array $data): void
    {
        file_put_contents(
            $this->getSettingsPath(),
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    public function mount(): void
    {
        $s = $this->loadSettings();

        $defaultPackages = [
            ['image' => null, 'label' => 'VIP Wristband Tickets',              'title' => 'Give your VIP guests a premium entry experience',           'copy' => 'With Our printed VIP wristbands, cleaner access control.',                                                                                           'price' => ''],
            ['image' => null, 'label' => 'Gate-Sale Ticket Printing',          'title' => 'Print ticket batches for fast sales at the entrance',        'copy' => 'Generate tickets in bulk, and sell them at entry with optional scanner support.',                                                                       'price' => ''],
            ['image' => null, 'label' => 'Online Ticketing & Event Management','title' => 'Sell online and let us manage your event.',                 'copy' => 'Let customers buy tickets online while our team manages verification, attendance, and event flow.',                                                   'price' => ''],
        ];

        $this->data = [
            'hero_title'    => $s['hero_title']    ?? 'Discover Unforgettable Events Near You',
            'hero_subtitle' => $s['hero_subtitle'] ?? 'Concerts, sports, workshops & more — book your spot in seconds.',
            'hero_banner_1' => $this->resolveStoredPath($s['hero_banner_1'] ?? null),
            'hero_banner_2' => $this->resolveStoredPath($s['hero_banner_2'] ?? null),
            'hero_banner_3' => $this->resolveStoredPath($s['hero_banner_3'] ?? null),
            'packages'      => array_map(function ($package) {
                return [
                    'image' => $this->resolveStoredPath($package['image'] ?? null),
                    'label' => $package['label'] ?? '',
                    'title' => $package['title'] ?? '',
                    'copy'  => $package['copy'] ?? '',
                    'price' => $package['price'] ?? '',
                ];
            }, $s['packages'] ?? $defaultPackages),
        ];
    }

    protected function resolveStoredPath($value): ?string
    {
        // Accept only plain string paths — reject UUID objects and arrays left by broken saves
        return is_string($value) && !empty($value) ? $value : null;
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Hero Text')
                    ->description('Heading and subtitle shown on the main intro slide.')
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Heading')
                            ->required()
                            ->maxLength(120)
                            ->placeholder('Discover Unforgettable Events Near You'),

                        Textarea::make('hero_subtitle')
                            ->label('Subtitle')
                            ->rows(2)
                            ->maxLength(200)
                            ->placeholder('Concerts, sports, workshops & more — book your spot in seconds.'),
                    ]),

                Section::make('Hero Banners')
                    ->description('Background images that cycle in the hero slideshow. Leave blank to keep the current default.')
                    ->schema([
                        FileUpload::make('hero_banner_1')
                            ->label('Banner 1')
                            ->image()
                            ->disk('public')
                            ->directory('hero-banners')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->nullable()
                            ->multiple(false),

                        FileUpload::make('hero_banner_2')
                            ->label('Banner 2')
                            ->image()
                            ->disk('public')
                            ->directory('hero-banners')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->nullable()
                            ->multiple(false),

                        FileUpload::make('hero_banner_3')
                            ->label('Banner 3')
                            ->image()
                            ->disk('public')
                            ->directory('hero-banners')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->nullable()
                            ->multiple(false),
                    ])
                    ->columns(3),

                Section::make('Ticket Packages')
                    ->description('Slides shown in the "Hosting an event?" hero panel. Upload a photo and fill in the text for each package.')
                    ->schema([
                        Repeater::make('packages')
                            ->label('')
                            ->schema([
                                FileUpload::make('image')
                                    ->label('Photo')
                                    ->image()
                                    ->disk('public')
                                    ->directory('packages')
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                                    ->maxSize(5120)
                                    ->nullable()
                                    ->columnSpanFull(),

                                TextInput::make('label')
                                    ->label('Pill / badge text')
                                    ->required()
                                    ->maxLength(60)
                                    ->placeholder('VIP Wristband Tickets'),

                                TextInput::make('title')
                                    ->label('Heading')
                                    ->required()
                                    ->maxLength(120)
                                    ->placeholder('Give your VIP guests a premium entry experience'),

                                TextInput::make('price')
                                    ->label('Starting price')
                                    ->maxLength(60)
                                    ->placeholder('e.g. From UGX 50,000 · Contact for pricing'),

                                Textarea::make('copy')
                                    ->label('Body text')
                                    ->rows(2)
                                    ->maxLength(300)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->minItems(1)
                            ->maxItems(6)
                            ->reorderable()
                            ->addActionLabel('Add package slide'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        // getState() validates the form AND moves uploaded files from temp to permanent storage,
        // returning proper string paths instead of Livewire UUID references.
        $data = $this->form->getState();

        $settings = $this->loadSettings();

        $settings['hero_title']    = $data['hero_title']    ?? '';
        $settings['hero_subtitle'] = $data['hero_subtitle'] ?? '';
        $settings['hero_banner_1'] = $this->resolveStoredPath($data['hero_banner_1'] ?? null);
        $settings['hero_banner_2'] = $this->resolveStoredPath($data['hero_banner_2'] ?? null);
        $settings['hero_banner_3'] = $this->resolveStoredPath($data['hero_banner_3'] ?? null);

        $packages = [];
        foreach ($data['packages'] ?? [] as $package) {
            $packages[] = [
                'image' => $this->resolveStoredPath($package['image'] ?? null),
                'label' => $package['label'] ?? '',
                'title' => $package['title'] ?? '',
                'copy'  => $package['copy'] ?? '',
                'price' => $package['price'] ?? '',
            ];
        }
        $settings['packages'] = $packages;

        $this->saveSettings($settings);

        Notification::make()
            ->title('Home page content saved.')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isSuperAdmin();
    }
}