<?php

namespace App\Console\Commands;

use App\Traits\LoggerTrait;
use Illuminate\Console\Command;

class TestCron extends Command
{
    use LoggerTrait;

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
        $this->createLog([
            'type' => 'info',
            'message' => "Probando que funcione cron (temporal)",
        ]);
    }
}
