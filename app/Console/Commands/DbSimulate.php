<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\ExamSchedule;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class DbSimulate extends Command
{
    protected $signature = 'db:simulate
                            {--students=10 : Number of test students}
                            {--scores=50 : Number of test scores}
                            {--cleanup : Remove all test data before creating}
                            {--force : Skip confirmation}';

    protected $description = 'Generate fake test data for simulation';

    public function handle(): int
    {
        $studentCount = (int) $this->option('students');
        $scoreCount = (int) $this->option('scores');
        $cleanup = $this->option('cleanup');
        $force = $this->option('force');

        $this->info("=== Database Simulation ===");
        $this->info("Students: {$studentCount}, Scores: {$scoreCount}");
        $this->newLine();

        // Check if we have departments and subjects
        $deptCount = Department::count();
        $subjectCount = Subject::count();

        if ($deptCount === 0 || $subjectCount === 0) {
            $this->error('Need at least 1 department and 1 subject. Run seeder first:');
            $this->line('  php artisan db:seed');
            return 1;
        }

        // Cleanup
        if ($force) {
            $deleted = User::where('email', 'LIKE', '%@simulate.test%')->delete();
            $this->info("Removed {$deleted} test students");
        } elseif ($cleanup || $this->confirm('Remove existing test students and scores first?', false)) {
            $deleted = User::where('email', 'LIKE', '%@simulate.test%')->delete();
            $this->info("Removed {$deleted} test students");
        }

        if (!$force) {
            $this->newLine();
            if (!$this->confirm("Create {$studentCount} test students and ~{$scoreCount} test scores?", true)) {
                $this->warn('Cancelled.');
                return 0;
            }
        }

        $bar = $this->output->createProgressBar($studentCount + $scoreCount);
        $bar->start();

        $startTime = microtime(true);
        $depts = Department::all();
        $subjects = Subject::all();
        $adminId = \App\Models\Admin::first()->id ?? 1;

        // Create test students
        $testStudents = [];
        for ($i = 1; $i <= $studentCount; $i++) {
            $dept = $depts->random();

            $student = User::create([
                'name' => "Test Student {$i}",
                'email' => "simulate{$i}@simulate.test",
                'phone' => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'password' => Hash::make('password123'),
                'department_id' => $dept->id,
                'nomor_ujian' => 'SIM' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'is_active' => true,
            ]);

            $testStudents[] = $student;
            $bar->advance();
        }

        // Create test scores
        for ($i = 0; $i < $scoreCount; $i++) {
            $student = collect($testStudents)->random();
            $subject = $subjects->random();

            Score::forceCreate([
                'user_id' => $student->id,
                'subject_id' => $subject->id,
                'score' => fake()->numberBetween(30, 100),
                'input_by_admin_id' => $adminId,
                'is_published' => fake()->boolean(70),
                'published_at' => fake()->boolean(70) ? now() : null,
            ]);

            $bar->advance();
        }

        $bar->finish();

        $duration = round(microtime(true) - $startTime, 2);

        $this->newLine(2);
        $this->info('=== Simulation Complete ===');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Students Created', number_format($studentCount)],
                ['Scores Created', number_format($scoreCount)],
                ['Duration', "{$duration}s"],
                ['Test Login', 'simulate1@simulate.test / password123'],
            ]
        );

        return 0;
    }
}
