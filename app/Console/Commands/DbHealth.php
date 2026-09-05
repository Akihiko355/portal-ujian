<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DbHealth extends Command
{
    protected $signature = 'db:health';
    protected $description = 'Check database connection and integrity';

    public function handle(): int
    {
        $this->info('=== Database Health Check ===');
        $this->newLine();

        $checks = [];
        $passed = 0;
        $failed = 0;

        // 1. Connection
        try {
            DB::connection()->getPdo();
            $checks[] = ['Connection', 'OK', 'green'];
            $passed++;
        } catch (\Exception $e) {
            $checks[] = ['Connection', 'FAILED: ' . $e->getMessage(), 'red'];
            $failed++;
        }

        // 2. Required tables exist
        $requiredTables = [
            'admins', 'users', 'departments', 'subjects',
            'exam_schedules', 'scores', 'failed_login_attempts',
        ];

        foreach ($requiredTables as $table) {
            if (Schema::hasTable($table)) {
                $count = DB::table($table)->count();
                $checks[] = ["Table: {$table}", "OK ({$count} rows)", 'green'];
                $passed++;
            } else {
                $checks[] = ["Table: {$table}", 'MISSING', 'red'];
                $failed++;
            }
        }

        // 3. Critical indexes exist
        $indexChecks = [
            'users' => ['email', 'department_id'],
            'scores' => ['user_id', 'subject_id'],
            'failed_login_attempts' => ['email', 'ip_address'],
        ];

        foreach ($indexChecks as $table => $columns) {
            foreach ($columns as $col) {
                if (Schema::hasColumn($table, $col)) {
                    $checks[] = ["Index: {$table}.{$col}", 'OK', 'green'];
                    $passed++;
                } else {
                    $checks[] = ["Index: {$table}.{$col}", 'MISSING', 'red'];
                    $failed++;
                }
            }
        }

        // 4. Foreign key integrity (basic)
        try {
            $orphanedScores = DB::table('scores')
                ->leftJoin('users', 'scores.user_id', '=', 'users.id')
                ->whereNull('users.id')
                ->count();

            if ($orphanedScores > 0) {
                $checks[] = ["FK: scores.user_id", "WARNING: {$orphanedScores} orphaned", 'yellow'];
            } else {
                $checks[] = ["FK: scores.user_id", 'OK', 'green'];
                $passed++;
            }
        } catch (\Exception $e) {
            $checks[] = ["FK: scores.user_id", 'ERROR', 'red'];
            $failed++;
        }

        // Display
        foreach ($checks as [$label, $status, $color]) {
            $icon = match ($color) {
                'green' => '✅',
                'red' => '❌',
                'yellow' => '⚠️',
                default => '  ',
            };
            $this->line(sprintf(
                "%s %-35s %s",
                $icon,
                $label,
                $status
            ));
        }

        $this->newLine();
        $this->info("Results: {$passed} passed, {$failed} failed");

        return $failed > 0 ? 1 : 0;
    }
}
