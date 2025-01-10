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
        
        // \Stripe\Stripe::setApiKey(env('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'));
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
    //         $response = Http::withToken($this->clientSecretStripeS)
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

    // public function getPaymentInfo($paymentReference)
    // {
    //     try {
    //         // Recuperar información del PaymentIntent
    //         // $paymentIntent = PaymentIntent::retrieve($paymentReference);
    //         $stripeSecret = env('STRIPE_SECRET_ACCOUNT_SECONDARY');

    //         // Realizar la solicitud a Stripe
    //         $response = Http::withToken($stripeSecret)->get("https://api.stripe.com/v1/payment_intents/{$paymentReference}");
  
    //         // // Retornar información útil
    //         // return response()->json([
    //         //     'status' => 'success',
    //         //     'payment_id' => $paymentIntent->id,
    //         //     'amount' => $paymentIntent->amount / 100, // Stripe usa centavos
    //         //     'currency' => $paymentIntent->currency,
    //         //     'status' => $paymentIntent->status,
    //         //     'metadata' => $paymentIntent->metadata, // Información adicional (opcional)
    //         // ]);

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
    //         ], 400);
    //     }
    // }

    public function getPaymentInfo($paymentReference)
    {
        $stripeSecret = env('STRIPE_SECRET_ACCOUNT_SECONDARY');
    
        // Configurar el curl
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.stripe.com/v1/payment_intents/{$paymentReference}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $stripeSecret",
        ]);
    
        // Ejecutar la solicitud
        $response = curl_exec($ch);
    
        // Manejo de errores
        if (curl_errno($ch)) {
            return response()->json([
                'status' => 'error',
                'message' => curl_error($ch),
            ], 500);
        }
    
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // Procesar la respuesta
        $data = json_decode($response, true);
    
        if ($httpCode == 200) {
            return response()->json([
                'status' => 'success',
                'payment_id' => $data['id'],
                'amount' => $data['amount'] / 100,
                'currency' => $data['currency'],
                'status' => $data['status'],
                'metadata' => $data['metadata'],
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => $data['error']['message'] ?? 'An error occurred',
            ], $httpCode);
        }
    }    
}