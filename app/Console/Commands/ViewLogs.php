<?php

namespace App\Console\Commands;

use App\Models\FailedLoginAttempt;
use Illuminate\Console\Command;

class ViewLogs extends Command
{
    protected $signature = 'logs:view {--type= : Filter by type: admin_success, admin_failed, admin_logout, web_success, web_failed, web_register, web_logout} {--limit=20 : Number of logs to show}';
    protected $description = 'View login activity logs';

    public function handle()
    {
        $query = FailedLoginAttempt::orderBy('attempted_at', 'desc');

        if ($type = $this->option('type')) {
            $query->where('guard_type', $type);
        }

        $logs = $query->take($this->option('limit'))->get();

        if ($logs->isEmpty()) {
            $this->warn('No logs found.');
            return;
        }

        $this->table(
            ['Time', 'Email', 'IP', 'Type'],
            $logs->map(fn($l) => [
                $l->attempted_at->format('d M H:i:s'),
                $l->email,
                $l->ip_address,
                $l->guard_type,
            ])
        );
    }
}