<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    protected $signature = 'admin:reset-password {email?}';
    protected $description = 'Reset admin password';

    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Admin email');

        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            $this->error("Admin with email {$email} not found!");
            return 1;
        }

        $password = $this->secret('New password');

        $admin->update(['password' => Hash::make($password)]);

        $this->info("Password for {$email} has been reset!");
        return 0;
    }
}