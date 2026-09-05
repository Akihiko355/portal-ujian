<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;

class ListAdmins extends Command
{
    protected $signature = 'admin:list';
    protected $description = 'List all admin accounts';

    public function handle()
    {
        $admins = Admin::all(['id', 'name', 'email', 'role', 'is_active', 'last_login_at']);

        if ($admins->isEmpty()) {
            $this->warn('No admin accounts found.');
            return;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Role', 'Active', 'Last Login'],
            $admins->map(fn($a) => [
                $a->id,
                $a->name,
                $a->email,
                $a->role,
                $a->is_active ? 'Yes' : 'No',
                $a->last_login_at ? $a->last_login_at->format('d M Y H:i') : 'Never',
            ])
        );
    }
}