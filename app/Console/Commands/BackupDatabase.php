<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database {--remote}';
    protected $description = 'Backup database locally or to remote storage. Use --remote to push to secondary location.';

    public function handle(): int
    {
        $isRemote = $this->option('remote');
        
        if ($isRemote) {
            return $this->pushToRemote();
        }
        
        return $this->backupLocally();
    }

    private function backupLocally(): int
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup-{$timestamp}.sql";
            $backupDir = storage_path('backups');
            
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0755, true);
            }
            
            $backupPath = "{$backupDir}/{$filename}";
            $dbName = config('database.connections.mysql.database');
            $dbUser = config('database.connections.mysql.username');
            $dbPass = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            
            // Build mysqldump command
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s %s > %s',
                escapeshellarg($dbUser),
                escapeshellarg($dbPass),
                escapeshellarg($dbHost),
                escapeshellarg($dbName),
                escapeshellarg($backupPath)
            );
            
            exec($command, $output, $returnCode);
            
            if ($returnCode === 0 && file_exists($backupPath)) {
                $fileSize = filesize($backupPath);
                Log::info("Database backup created successfully", [
                    'file' => $filename,
                    'size_bytes' => $fileSize,
                    'path' => $backupPath,
                ]);
                
                $this->info("✓ Local backup created: {$filename} ({$fileSize} bytes)");
                
                // Cleanup old backups (keep last 30 days)
                $this->cleanupOldBackups($backupDir);
                
                return Command::SUCCESS;
            } else {
                throw new \Exception("mysqldump failed with code {$returnCode}");
            }
        } catch (\Throwable $e) {
            Log::error("Database backup failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            $this->error("✗ Backup failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function pushToRemote(): int
    {
        try {
            $backupDir = storage_path('backups');
            
            if (!is_dir($backupDir)) {
                throw new \Exception("No local backups found to push");
            }
            
            // Get most recent backup
            $files = array_filter(
                scandir($backupDir),
                fn($f) => str_starts_with($f, 'backup-') && str_ends_with($f, '.sql')
            );
            
            if (empty($files)) {
                throw new \Exception("No backup files found");
            }
            
            rsort($files);
            $latestBackup = $files[0];
            $localPath = "{$backupDir}/{$latestBackup}";
            $remoteDir = config('backup.remote_dir', storage_path('backups/remote'));
            
            if (!is_dir($remoteDir)) {
                mkdir($remoteDir, 0755, true);
            }
            
            $remotePath = "{$remoteDir}/{$latestBackup}";
            
            if (copy($localPath, $remotePath)) {
                $fileSize = filesize($remotePath);
                Log::info("Backup pushed to remote", [
                    'file' => $latestBackup,
                    'size_bytes' => $fileSize,
                    'remote_path' => $remotePath,
                ]);
                
                $this->info("✓ Backup pushed to remote: {$latestBackup}");
                
                // Cleanup old remote backups (keep last 4 weeks)
                $this->cleanupOldBackups($remoteDir, 28);
                
                return Command::SUCCESS;
            } else {
                throw new \Exception("Failed to copy backup to remote");
            }
        } catch (\Throwable $e) {
            Log::error("Backup remote push failed", [
                'error' => $e->getMessage(),
            ]);
            
            $this->error("✗ Remote push failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function cleanupOldBackups(string $dir, int $daysToKeep = 30): void
    {
        $files = scandir($dir);
        $cutoffTime = now()->subDays($daysToKeep)->timestamp;
        
        foreach ($files as $file) {
            if (str_starts_with($file, 'backup-') && str_ends_with($file, '.sql')) {
                $filePath = "{$dir}/{$file}";
                if (filemtime($filePath) < $cutoffTime) {
                    unlink($filePath);
                    Log::info("Deleted old backup", ['file' => $file]);
                }
            }
        }
    }
}
