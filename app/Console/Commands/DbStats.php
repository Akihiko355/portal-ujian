<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbStats extends Command
{
    protected $signature = 'db:stats';
    protected $description = 'Show database statistics and table sizes';

    public function handle(): int
    {
        $this->info('=== Database Statistics ===');
        $this->newLine();

        $tables = [
            'admins' => 'Admins',
            'users' => 'Students',
            'departments' => 'Departments',
            'subjects' => 'Subjects',
            'exam_schedules' => 'Exam Schedules',
            'scores' => 'Scores',
            'failed_login_attempts' => 'Failed Logins',
            'personal_access_tokens' => 'Sessions',
        ];

        $rows = [];

        foreach ($tables as $table => $label) {
            if (!Schema::hasTable($table)) {
                continue;
            }

            $count = DB::table($table)->count();

            // Estimate size (SQLite specific)
            $size = 0;
            try {
                if (DB::connection()->getDriverName() === 'sqlite') {
                    $fileSize = filesize(database_path('database.sqlite')) ?: 0;
                    // Rough estimate per table
                    $totalRows = DB::table('sqlite_sequence')->where('name', $table)->value('seq') ?? $count;
                    if ($totalRows > 0 && $fileSize > 0) {
                        $size = round(($count / $totalRows) * $fileSize / 1024, 1);
                    }
                }
            } catch (\Exception $e) {
                // ignore
            }

            $rows[] = [
                $label,
                number_format($count),
                $size > 0 ? "{$size} KB" : '-',
            ];
        }

        $this->table(['Table', 'Rows', 'Est. Size'], $rows);

        // Key metrics
        $this->newLine();
        $this->info('Key Metrics:');

        $metrics = [];

        $totalStudents = DB::table('users')->count();
        $totalScores = DB::table('scores')->count();
        $publishedScores = DB::table('scores')->where('is_published', true)->count();
        $failedToday = DB::table('failed_login_attempts')
            ->whereDate('attempted_at', now()->toDateString())
            ->count();
        $admins = DB::table('admins')->where('is_active', true)->count();

        $metrics[] = ['Active Students', number_format($totalStudents)];
        $metrics[] = ['Total Scores', number_format($totalScores)];
        $metrics[] = ['Published Scores', number_format($publishedScores) . ' (' . ($totalScores > 0 ? round($publishedScores / $totalScores * 100) : 0) . '%)'];
        $metrics[] = ['Failed Logins Today', number_format($failedToday)];
        $metrics[] = ['Active Admins', number_format($admins)];

        $this->table(['Metric', 'Value'], $metrics);

        // DB file size
        try {
            $dbPath = database_path('database.sqlite');
            if (file_exists($dbPath)) {
                $size = round(filesize($dbPath) / 1024, 2);
                $this->newLine();
                $this->info("SQLite file: {$dbPath} ({$size} KB)");
            }
        } catch (\Exception $e) {
            // ignore for non-sqlite
        }

        return 0;
    }
}
