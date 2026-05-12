<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use UnitEnum;

class BackupVaultPage extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $navigationLabel = 'Backup Vault';

    protected static string|UnitEnum|null $navigationGroup = 'Operations';

    protected static ?int $navigationSort = 90;

    protected string $view = 'filament.pages.backup-vault-page';

    public string $search = '';

    public function getRemoteDirectory(): string
    {
        return (string) env('BACKUP_REMOTE_DIR', storage_path('backups/remote'));
    }

    public function getBackups(): array
    {
        $directory = $this->getRemoteDirectory();

        if (! is_dir($directory)) {
            return [];
        }

        $items = scandir($directory);

        if ($items === false) {
            return [];
        }

        $search = Str::lower(trim($this->search));

        return collect($items)
            ->reject(fn (string $file) => in_array($file, ['.', '..'], true))
            ->filter(function (string $file) use ($directory): bool {
                if (! preg_match('/\.(sql|gz|enc)$/i', $file)) {
                    return false;
                }

                return is_file($directory . DIRECTORY_SEPARATOR . $file);
            })
            ->map(function (string $file) use ($directory): array {
                $path = $directory . DIRECTORY_SEPARATOR . $file;

                return [
                    'name' => $file,
                    'size_bytes' => filesize($path) ?: 0,
                    'modified_at' => Carbon::createFromTimestamp(filemtime($path) ?: time()),
                ];
            })
            ->when($search !== '', function ($collection) use ($search) {
                return $collection->filter(fn (array $row) => Str::contains(Str::lower($row['name']), $search));
            })
            ->sortByDesc(fn (array $row) => $row['modified_at']->timestamp)
            ->values()
            ->all();
    }

    public function runRemoteBackup(): void
    {
        if (! static::canAccess()) {
            abort(403);
        }

        Artisan::call('backup:database', ['--remote' => true]);
        $output = trim(Artisan::output());

        Notification::make()
            ->title('Remote backup completed')
            ->body($output !== '' ? $output : 'Backup command finished successfully.')
            ->success()
            ->send();
    }

    public function download(string $filename)
    {
        if (! static::canAccess()) {
            abort(403);
        }

        $filename = basename($filename);
        $directory = $this->getRemoteDirectory();
        $directoryReal = realpath($directory);

        if ($directoryReal === false) {
            abort(404, 'Backup directory not found.');
        }

        $filePath = $directoryReal . DIRECTORY_SEPARATOR . $filename;
        $fileReal = realpath($filePath);

        if ($fileReal === false || ! is_file($fileReal)) {
            abort(404, 'Backup file not found.');
        }

        $directoryPrefix = rtrim($directoryReal, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (! Str::startsWith($fileReal, $directoryPrefix)) {
            abort(403, 'Invalid backup file path.');
        }

        return response()->download($fileReal, $filename);
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->isAdmin();
    }
}
