<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Http;
use Stripe\Stripe;
use Stripe\PaymentIntent;

trait StripeTrait
{

    private $clientIdStripe;
    private $clientSecretStripe;
    private $apiUrlStripe;

    public function __construct()
    {
        // $this->clientIdStripe = config('services.stripe.clientID');
        // $this->clientSecretStripe = config('services.stripe.secretKey');
        // $this->apiUrlStripe = config('services.stripe.apiUrl');
        Stripe::setApiKey(env('STRIPE_SECRET'));
    }    

    /**
     * Realiza una solicitud a la API de Stripe.
     *
     * @param string $endpoint
     * @param string $method
     * @param array $params
     * @return array
     */
    public function stripeRequest($endpoint, $method = 'GET', $params = [])
    {
        try {
            $response = Http::withToken($this->clientSecretStripe)->$method($this->apiUrlStripe . $endpoint, $params);

            // Validar si la solicitud fue exitosa
            if ($response->successful()) {
                return [
                    'status' => 'success',
                    'data' => $response->json(),
                ];
            }

            // Manejar errores
            return [
                'status' => 'error',
                'message' => $response->json('error.message'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    // public function getPaymentInfo($paymentReference)
    // {
    //     try {    
    //         // Realizar la solicitud a Stripe
    //         // /{$paymentReference}
    //         $response = Http::withToken($this->clientSecretStripe)
    //                     ->get($this->apiUrlStripe . "/v1/payment_intents/{$paymentReference}");
    
    //         // Verificar si la respuesta es exitosa
    //         if ($response->successful()) {
    //             $data = $response->json();
    
    //             return response()->json([
    //                 'status' => 'success',
    //                 'payment_id' => $data['id'],
    //                 'amount' => $data['amount'] / 100, // Stripe usa centavos
    //                 'currency' => $data['currency'],
    //                 'status' => $data['status'],
    //                 'metadata' => $data['metadata'],
    //             ]);
    //         } else {
    //             return response()->json([
    //                 'status' => 'error',
    //                 'message' => $response->json('error.message'),
    //             ], $response->status());
    //         }
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => 'error',
    //             'message' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    public function getPaymentInfo($paymentReference)
    {
        try {
            // Recuperar información del PaymentIntent
            // $paymentIntent = PaymentIntent::retrieve($paymentReference);
            $paymentIntent = PaymentIntent::retrieve($paymentReference);
    
            // Retornar información útil
            return response()->json([
                'status' => 'success',
                'payment_id' => $paymentIntent->id,
                'amount' => $paymentIntent->amount / 100, // Stripe usa centavos
                'currency' => $paymentIntent->currency,
                'status' => $paymentIntent->status,
                'metadata' => $paymentIntent->metadata, // Información adicional (opcional)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }    
}