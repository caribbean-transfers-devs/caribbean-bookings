<?php

namespace App\Traits;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response;
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

    /**
     * Realiza una solicitud cURL a la API de Stripe
     * 
     * @param string $url URL completa del endpoint
     * @param string $apiKey Clave API de Stripe
     * @param string $method Método HTTP (GET, POST, etc.)
     * @param array|null $data Datos a enviar en la solicitud (para POST)
     * @return array Respuesta de la API
     */
    private function makeCurlRequest($url, $apiKey, $method = 'GET', $data = null)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
        }/* elseif ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }*/
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL Error: " . $error);
        }
        
        return [
            'status' => $httpCode,
            'body' => json_decode($response, true) ?: $response
        ];
    }

    /*********************************************************************/
    /****************************** PAYOUTS ******************************/
    /*********************************************************************/
    
    // Método para buscar los pagos
    public function getPayoutsV1($balanceTransaction)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/payouts?expand[]=data.balance_transaction&expand[]=data.destination&limit=100";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeS);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPayoutV1($paymentReference)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/payouts/" . $paymentReference . "?expand[]=balance_transaction&expand[]=destination";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeS);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBalancePayoutV1($paymentReference)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/balance_transactions?payout=". $paymentReference ."&expand[]=data.source&expand[]=data.source.balance_transaction&limit=2000";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeS);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }    

    // Método para buscar los pagos
    public function getPayoutsV2($balanceTransaction)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/payouts?expand[]=data.balance_transaction&expand[]=data.destination";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function getPayoutV2($paymentReference)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/payouts/" . $paymentReference . "?expand[]=balance_transaction&expand[]=destination";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBalancePayoutV2($paymentReference)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/balance_transactions?payout=". $paymentReference ."&expand[]=data.source&expand[]=data.source.balance_transaction&limit=2000";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }    

    public function getLast10Payouts() {
        try {
            $url = $this->apiUrlStripe . "/v1/payouts?expand[]=data.balance_transaction&expand[]=data.destination&limit=10";
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                throw new \Exception($errorMsg);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /*********************************************************************/
    /****************************** BALANCE ******************************/
    /*********************************************************************/

    // Método para buscar un balance por ID
    public function getBalanceInfoV1($balanceTransaction)
    {
        try {
            $url = $this->apiUrlStripe . "/v1/balance_transactions/" . $balanceTransaction;
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeS);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
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
            $url = $this->apiUrlStripe . "/v1/balance_transactions/" . $balanceTransaction;
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve balance';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /*****************************************************************************/
    /****************************** PAYMENT INTENTS ******************************/
    /*****************************************************************************/
    public function getPaymentIntentsV1($paymentReference, $data = [])
    {
        try {
            $url = $this->apiUrlStripe . "/v1/payment_intents/" . $paymentReference;
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeS);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve payment intents';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPaymentIntentsV2($paymentReference, $data = [])
    {
        try {
            $url = $this->apiUrlStripe . "/v1/payment_intents/" . $paymentReference;
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve payment intents';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /*********************************************************************/
    /****************************** CHARGES ******************************/
    /*********************************************************************/

    // Método para buscar un cargo por referencia
    public function getChargesV1($paymentReference, $data = [])
    {
        try {
            $url = $this->apiUrlStripe . "/v1/charges/" . $paymentReference;
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeS, 'POST', $data);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve charge';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // Método para buscar un cargo por referencia
    public function getChargesV2($paymentReference, $data = [])
    {
        try {
            $url = $this->apiUrlStripe . "/v1/charges/" . $paymentReference;
            $response = $this->makeCurlRequest($url, $this->clientSecretStripeP, 'POST', $data);
            
            if ($response['status'] >= 200 && $response['status'] < 300) {
                return response()->json($response['body']);
            } else {
                $errorMsg = $response['body']['error']['message'] ?? 'Failed to retrieve charge';
                return response()->json([
                    'status' => 'error',
                    'error' => $errorMsg,
                    'message' => $errorMsg,
                ], $response['status']);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}