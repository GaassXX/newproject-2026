<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProjectInitialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Project Initialization';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Only run migrate:fresh if there's a .fresh_init_flag file (set by first setup script)
        // Otherwise, just migrate normally to preserve user data
        $freshInitFlag = storage_path('.fresh_init_flag');

        if (file_exists($freshInitFlag)) {
            $this->info('🚀 Fresh initialization mode detected. Running migrate:fresh...');
            $this->call('migrate:fresh', [
                '--force' => true,
            ]);
            unlink($freshInitFlag);
        } else {
            $this->info('💾 Production mode: Running regular migrate (preserving data)...');
            $this->call('migrate', [
                '--force' => true,
            ]);
        }

        $this->call('shield:generate', [
            '--all' => true,
            '--panel' => 'admin',
        ]);

        // Only seed if in fresh init mode
        if (!file_exists($freshInitFlag)) {
            $this->info('📦 Seeding database...');
            $this->call('db:seed', [
                '--force' => true,
            ]);
        }

        $this->call('filament:optimize-clear');
        $this->call('optimize:clear');
    }
}
