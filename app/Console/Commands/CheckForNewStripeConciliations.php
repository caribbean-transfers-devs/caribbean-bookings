<?php

namespace App\Console\Commands;

use App\Traits\LoggerTrait;
use App\Traits\StripeConciliationTrait;
use Illuminate\Console\Command;

class CheckForNewStripeConciliations extends Command
{
    use LoggerTrait, StripeConciliationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-for-new-stripe-conciliations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revisa las últimos 10 payouts de stripe, y las guarda/actualiza si no están conciliadas en el sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->createLog([
            'type' => 'info',
            'category' => $this->signature,
            'message' => "Iniciando $this->signature",
        ]);
        
        $this->initStripe();
        $this->checkForNewConciliations();
        
        $this->createLog([
            'type' => 'info',
            'category' => $this->signature,
            'message' => "Proceso $this->signature terminado",
        ]);
    }
}
