<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Deploy extends Command
{
    protected $signature = 'app:deploy';
    protected $description = 'Run full deployment setup';

    public function handle()
    {
        $this->info('=== Portal Ujian Deployment ===');
        $this->newLine();

        $this->info('1. Installing dependencies...');
        $this->call('composer', ['install', '--no-dev', '--optimize-autoloader']);

        $this->info('2. Installing NPM packages...');
        exec('npm install --production 2>&1', $output);
        $this->info(implode("\n", $output));

        $this->info('3. Generating app key...');
        $this->call('key:generate', ['--force' => true]);

        $this->info('4. Running migrations...');
        $this->call('migrate', ['--force' => true]);

        $this->info('5. Seeding database...');
        $this->call('db:seed', ['--force' => true]);

        $this->info('6. Caching config...');
        $this->call('config:cache');
        $this->call('event:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        $this->info('7. Setting permissions...');
        exec('chmod -R 775 storage bootstrap/cache 2>/dev/null');

        $this->newLine();
        $this->info('=== Deployment Complete! ===');
        $this->info('Admin login: admin@portal-ujian.com / password');
    }
}