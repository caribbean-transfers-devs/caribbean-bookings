<?php

namespace App\Console\Commands;

use App\Traits\LoggerTrait;
use App\Traits\StripeConciliationTrait;
use Illuminate\Console\Command;

class InitializeAllStripeRefunds extends Command
{
    use LoggerTrait, StripeConciliationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialize-all-stripe-refunds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre todas las devoluciones existentes en stripe, y las guarda en el sistema si no existen';

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
        $this->initializeAllRefunds();
        
        $this->createLog([
            'type' => 'info',
            'category' => $this->signature,
            'message' => "Proceso $this->signature terminado",
        ]);
    }
}
