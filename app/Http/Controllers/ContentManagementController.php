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
            ['image' => null, 'label' => 'VIP Wristband Tickets',           'title' => 'Give your VIP guests a premium entry experience',           'copy' => 'With Our printed VIP wristbands, cleaner access control.'],
            ['image' => null, 'label' => 'Gate-Sale Ticket Printing',        'title' => 'Print ticket batches for fast sales at the entrance',        'copy' => 'Generate tickets in bulk, and sell them at entry with optional scanner support.'],
            ['image' => null, 'label' => 'Online Ticketing & Event Management', 'title' => 'Sell online and let us manage your event.',               'copy' => 'Let customers buy tickets online while our team manages verification, attendance, and event flow.'],
        ];

        $this->data = [
            'hero_title'    => $s['hero_title']    ?? 'Discover Unforgettable Events Near You',
            'hero_subtitle' => $s['hero_subtitle'] ?? 'Concerts, sports, workshops & more — book your spot in seconds.',
            'hero_banner_1' => $this->forceString($s['hero_banner_1'] ?? null),
            'hero_banner_2' => $this->forceString($s['hero_banner_2'] ?? null),
            'hero_banner_3' => $this->forceString($s['hero_banner_3'] ?? null),
            'packages'      => array_map(function ($package) {
                return [
                    'image' => $this->forceString($package['image'] ?? null),
                    'label' => $package['label'] ?? '',
                    'title' => $package['title'] ?? '',
                    'copy'  => $package['copy'] ?? '',
                ];
            }, $s['packages'] ?? $defaultPackages),
        ];
    }

    protected function forceString($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        if (is_array($value)) {
            return isset($value[0]) && is_string($value[0]) ? $value[0] : null;
        }
        
        return is_string($value) ? $value : null;
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
                            ->directory('hero-banners')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->multiple(false)
                            ->maxFiles(1)
                            ->storeFiles(false)
                            ->nullable(),

                        FileUpload::make('hero_banner_2')
                            ->label('Banner 2')
                            ->image()
                            ->directory('hero-banners')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->multiple(false)
                            ->maxFiles(1)
                            ->storeFiles(false)
                            ->nullable(),

                        FileUpload::make('hero_banner_3')
                            ->label('Banner 3')
                            ->image()
                            ->directory('hero-banners')
                            ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                            ->maxSize(5120)
                            ->multiple(false)
                            ->maxFiles(1)
                            ->storeFiles(false)
                            ->nullable(),
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
                                    ->directory('packages')
                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/gif'])
                                    ->maxSize(5120)
                                    ->multiple(false)
                                    ->maxFiles(1)
                                    ->storeFiles(false)
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
        // CRITICAL: Force convert all file fields to strings BEFORE validation
        $this->data['hero_banner_1'] = $this->forceString($this->data['hero_banner_1'] ?? null);
        $this->data['hero_banner_2'] = $this->forceString($this->data['hero_banner_2'] ?? null);
        $this->data['hero_banner_3'] = $this->forceString($this->data['hero_banner_3'] ?? null);
        
        if (isset($this->data['packages']) && is_array($this->data['packages'])) {
            foreach ($this->data['packages'] as $key => $package) {
                $this->data['packages'][$key]['image'] = $this->forceString($package['image'] ?? null);
            }
        }

        // Now validate
        $this->validate([
            'data.hero_title'          => 'required|string|max:120',
            'data.hero_subtitle'       => 'nullable|string|max:200',
            'data.hero_banner_1'       => 'nullable|string',
            'data.hero_banner_2'       => 'nullable|string',
            'data.hero_banner_3'       => 'nullable|string',
            'data.packages.*.image'    => 'nullable|string',
            'data.packages.*.label'    => 'required|string|max:60',
            'data.packages.*.title'    => 'required|string|max:120',
            'data.packages.*.copy'     => 'nullable|string|max:300',
        ]);

        $settings = $this->loadSettings();

        $settings['hero_title']    = $this->data['hero_title']    ?? '';
        $settings['hero_subtitle'] = $this->data['hero_subtitle'] ?? '';
        $settings['hero_banner_1'] = $this->forceString($this->data['hero_banner_1'] ?? null);
        $settings['hero_banner_2'] = $this->forceString($this->data['hero_banner_2'] ?? null);
        $settings['hero_banner_3'] = $this->forceString($this->data['hero_banner_3'] ?? null);
        
        $packages = [];
        foreach ($this->data['packages'] ?? [] as $package) {
            $packages[] = [
                'image' => $this->forceString($package['image'] ?? null),
                'label' => $package['label'] ?? '',
                'title' => $package['title'] ?? '',
                'copy'  => $package['copy'] ?? '',
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