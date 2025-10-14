<?php

namespace App\Console\Commands;

use App\Traits\LoggerTrait;
use App\Traits\StripeConciliationTrait;
use Illuminate\Console\Command;

class InitializeAllStripePaymentsConciliation extends Command
{
    use LoggerTrait, StripeConciliationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:initialize-all-stripe-payments-conciliation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recorre todos los payouts existentes en stripe, concilia los pagos relacionados, y guarda los payouts en el sistema si no existen';

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
        $this->initializeAllPaymentsConciliation();
        
        $this->createLog([
            'type' => 'info',
            'category' => $this->signature,
            'message' => "Proceso $this->signature terminado",
        ]);
    }
}
