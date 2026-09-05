<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Console\Command;

class Status extends Command
{
    protected $signature = 'app:status';
    protected $description = 'Show application status and statistics';

    public function handle()
    {
        $this->info('=== Portal Ujian Status ===');
        $this->newLine();

        $this->table(
            ['Metric', 'Count'],
            [
                ['Admins', Admin::count()],
                ['Users (Mahasiswa)', User::count()],
                ['Departments', Department::count()],
                ['Subjects', Subject::count()],
                ['Exam Schedules', ExamSchedule::count()],
                ['Scores (Total)', Score::count()],
                ['Scores (Published)', Score::where('is_published', true)->count()],
                ['Scores (Pending)', Score::where('is_published', false)->count()],
            ]
        );

        $this->newLine();
        $this->info('Server: ' . php_uname('s') . ' ' . php_uname('r'));
        $this->info('PHP: ' . phpversion());
        $this->info('Laravel: ' . app()->version());
    }
}