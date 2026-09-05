<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';
    protected $description = 'Backup SQLite database';

    public function handle()
    {
        $dbPath = database_path('database.sqlite');
        $backupDir = database_path('backups');

        if (!file_exists($dbPath)) {
            $this->error('Database file not found!');
            return 1;
        }

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sqlite';
        $backupPath = $backupDir . '/' . $filename;

        copy($dbPath, $backupPath);

        $size = round(filesize($backupPath) / 1024, 2);
        $this->info("Backup saved: {$backupPath} ({$size} KB)");
        return 0;
    }
}