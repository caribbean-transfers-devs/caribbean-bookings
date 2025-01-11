<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\PaymentIntent;

trait StripeTrait
{

    private $apiUrlStripe;
    
    private $clientIdStripeS;
    private $clientSecretStripeS;
    private $clientIdStripeP;
    private $clientSecretStripeP;

    public function __construct()
    {
        $this->apiUrlStripe = config('services.stripe.apiUrl');

        $this->clientIdStripeS = config('services.stripe.clientIDSecondary');
        $this->clientSecretStripeS = config('services.stripe.secretKeySecondary');
        $this->clientIdStripeP = config('services.stripe.clientIDPrimary');
        $this->clientSecretStripeP = config('services.stripe.secretKeyPrimary');
    }

    public function getBalanceInfo(){

    }

    public function getPaymentInfo($paymentReference)
    {
        try {    
            // Realizar la solicitud a Stripe
            $response = Http::withToken($this->clientSecretStripeS)
                        ->get($this->apiUrlStripe . "/v1/payment_intents/{$paymentReference}");
    
            // Verificar si la respuesta es exitosa
            if ($response->successful()) {
                $data = $response->json();                

                echo "Monto Cobrado: " . $charge->amount . "\n";
                echo "Tarifa de Stripe: " . $balanceTransaction->fee . "\n";
                echo "Total a Recibir: " . $balanceTransaction->net . "\n";


                if ($charge->status === 'succeeded') {
                    echo "El cargo fue aprobado.\n";
                } else {
                    echo "El cargo no fue aprobado.\n";
                }

                $refunds = $data->refunds->data;
                if (count($refunds) > 0) {
                    echo "Reembolsos realizados:\n";
                
                    foreach ($refunds as $refund) {
                        echo "- Reembolso ID: " . $refund->id . ", Monto: " . $refund->amount . "\n";
                
                        // Obtener el balance del reembolso
                        $refundTransaction = \Stripe\BalanceTransaction::retrieve($refund->balance_transaction);
                        echo "  Impacto del reembolso en balance: " . $refundTransaction->amount . "\n";
                        echo "  Tarifas asociadas al reembolso: " . $refundTransaction->fee . "\n";
                        echo "  Total neto del reembolso: " . $refundTransaction->net . "\n";
                    }
                } else {
                    echo "No hay reembolsos asociados a este cargo.\n";
                }                
    
                return response()->json([
                    'status' => 'success',
                    'payment_id' => $data['id'],
                    'amount' => $data['amount'] / 100, // Stripe usa centavos
                    'currency' => $data['currency'],
                    'status' => $data['status'],
                    'metadata' => $data['metadata'],
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => $response->json('error.message'),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }  
}