<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecutar un cron de prueba';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('TestCron ejecutado a las ' . now());
    }
}
