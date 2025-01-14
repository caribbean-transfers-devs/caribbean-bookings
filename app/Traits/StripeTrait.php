<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Http;

trait StripeTrait
{

    private $apiUrlStripe;
    private $clientIdStripeS;
    private $clientSecretStripeS;
    private $clientIdStripeP;
    private $clientSecretStripeP;

    public function initStripe()
    {
        $this->apiUrlStripe = config('services.stripe.apiUrl');
        $this->clientIdStripeS = config('services.stripe.clientIDSecondary');
        $this->clientSecretStripeS = config('services.stripe.secretKeySecondary');
        $this->clientIdStripeP = config('services.stripe.clientIDPrimary');
        $this->clientSecretStripeP = config('services.stripe.secretKeyPrimary');
    }

    // Método para buscar un balance por ID
    public function getBalanceInfoV1($balanceTransaction)
    {
        try {
            // Realizar la solicitud a Stripe
            $response = Http::withToken($this->clientSecretStripeS)
                        ->get($this->apiUrlStripe . "/v1/balance_transactions/". $balanceTransaction);
    
            // Verificar si la respuesta es exitosa
            if ($response->successful()) {    
                return response()->json($response->json());
            } else {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Failed to retrieve balance: ' . $response->body(),
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

    // Método para buscar un balance por ID
    public function getBalanceInfoV2($balanceTransaction)
    {
        try {
            // Realizar la solicitud a Stripe
            $response = Http::withToken($this->clientSecretStripeP)
                        ->get($this->apiUrlStripe . "/v1/balance_transactions/". $balanceTransaction);
    
            // Verificar si la respuesta es exitosa
            if ($response->successful()) {    
                return response()->json($response->json());
            } else {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Failed to retrieve balance: ' . $response->body(),
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

    // Método para buscar un cargo por referencia
    public function getPaymentInfoV1($paymentReference)
    {
        try {
            // Realizar la solicitud a Stripe
            $response = Http::withToken($this->clientSecretStripeS)
                        ->get($this->apiUrlStripe . "/v1/charges/". $paymentReference);
    
            // Verificar si la respuesta es exitosa
            if ($response->successful()) {    
                return response()->json($response->json());
                // return $response->json();
            } else {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Failed to retrieve charge: ' . $response->body(),
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

    // Método para buscar un cargo por referencia
    public function getPaymentInfoV2($paymentReference)
    {
        try {
            // Realizar la solicitud a Stripe
            $response = Http::withToken($this->clientSecretStripeP)
                        ->get($this->apiUrlStripe . "/v1/charges/". $paymentReference);
    
            // Verificar si la respuesta es exitosa
            if ($response->successful()) {    
                return response()->json($response->json());
                // return $response->json();
            } else {
                return response()->json([
                    'status' => 'error',
                    'error' => 'Failed to retrieve charge: ' . $response->body(),
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