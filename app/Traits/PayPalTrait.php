<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Http;

trait PayPalTrait
{

    private $clientId;
    private $clientSecret;
    private $apiUrl;

    public function initPayPal()
    {
        $this->clientId = config('services.paypal.clientID');
        $this->clientSecret = config('services.paypal.secretKey');
        $this->apiUrl = config('services.paypal.apiUrl');
    }    

    // Método para autenticar y obtener el token de acceso
    public function authenticate()
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post($this->apiUrl . '/v1/oauth2/token', [
                'grant_type' => 'client_credentials',
            ]);

        if ($response->successful()) {
            $accessToken = $response->json()['access_token'];
            // Guarda el token en el caché para futuras consultas
            cache(['paypal_access_token' => $accessToken], now()->addMinutes(55));
            return $accessToken;
        } else {
            throw new \Exception('Failed to authenticate with PayPal: ' . $response->body());
        }
    }

    // Método para obtener una orden por su referencia
    public function getOrderInfo($orderId)
    {
        try {
            // Verifica si el token de acceso ya está en caché
            $accessToken = cache('paypal_access_token') ?? $this->authenticate();

            // Realiza la solicitud para obtener la orden
            // Endpoint para obtener detalles de una orden por Referencia de transacción
            $response = Http::withToken($accessToken)
                            ->timeout(120) // Aumentar el timeout a 60 segundos
                            // ->withOptions([
                            //     'verify' => false, // Desactivar temporalmente la verificación SSL
                            //     'debug' => true,   // Habilitar logs de depuración
                            // ])            
                            ->get($this->apiUrl . '/v2/checkout/orders/' . $orderId);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                // Si el token expiró, vuelve a autenticarse y reintenta
                if ($response->status() == 401) {
                    $accessToken = $this->authenticate();
                    $response = Http::withToken($accessToken)
                                    ->timeout(120) // Aumentar el timeout a 60 segundos
                                    // ->withOptions([
                                    //     'verify' => false, // Desactivar temporalmente la verificación SSL
                                    //     'debug' => true,   // Habilitar logs de depuración
                                    // ])                    
                                    ->get($this->apiUrl . '/v2/checkout/orders/' . $orderId);

                    if ($response->successful()) {
                        return response()->json($response->json());
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => $response->json('error.message'),
                        ], $response->status());
                    }
                }
                return response()->json([
                    'status' => 'error',
                    'error' => 'Failed to retrieve order: ' . $response->body(),
                    'message' => $response->json('error.message')
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Método para buscar un pago por referencia
    public function getPaymentInfo($paymentReference)
    {
        try {
            // Verifica si el token de acceso ya está en caché
            $accessToken = cache('paypal_access_token') ?? $this->authenticate();

            // Realiza la solicitud para obtener el pago
            // Endpoint para obtener detalles de un pago por ID de transacción
            $response = Http::withToken($accessToken)
                            ->timeout(120) // Aumentar el timeout a 60 segundos
                            // ->withOptions([
                            //     'verify' => false, // Desactivar temporalmente la verificación SSL
                            //     'debug' => true,   // Habilitar logs de depuración
                            // ])
                            ->get($this->apiUrl . '/v2/payments/captures/' . $paymentReference);
    
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                // Si el token expiró, vuelve a autenticarse y reintenta
                if ($response->status() == 401) {
                    $accessToken = $this->authenticate();
                    $response = Http::withToken($accessToken)
                                    ->timeout(120) // Aumentar el timeout a 60 segundos
                                    // ->withOptions([
                                    //     'verify' => false, // Desactivar temporalmente la verificación SSL
                                    //     'debug' => true,   // Habilitar logs de depuración
                                    // ])
                                    ->get($this->apiUrl . '/v2/payments/captures/' . $paymentReference);
    
                    if ($response->successful()) {
                        return response()->json($response->json());
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => $response->json('error.message'),
                        ], $response->status());
                    }
                }
                return response()->json([
                    'status' => 'error',
                    'error' => 'Failed to retrieve payment: ' . $response->body(),
                    'message' => $response->json('error.message')
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