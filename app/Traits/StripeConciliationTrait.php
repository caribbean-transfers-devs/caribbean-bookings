<?php
namespace App\Traits;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\ReservationsRefund;
use App\Models\StripePayments;
use Carbon\Carbon;
use Exception;


trait StripeConciliationTrait {

    use LoggerTrait, StripeTrait;

    public function initializeAllPayoutsInDb() {
        // log data
        $process_id = $this->generateRandomProcessId();
        $log_category = 'initializeAllPayoutsInDb';
        $counter = 0;

        $allPayoutIds = [];
        $startingAfter = null;

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Comenzando proceso de $log_category",
        ]);
        do {
            try {
                $response = $this->getPayouts($startingAfter);
                $decoded = $response->getData(true);
                
                $payouts = $decoded['data'] ?? [];
                $payoutsCount = sizeof($payouts);
                foreach($payouts as $payout) {
                    $allPayoutIds[] = $payout['id'];
                }
    
                $hasMore = $decoded['has_more'] ?? false;
                $startingAfter = !empty($payouts) ? end($payouts)['id'] : null;

                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'info',
                    'category' => $log_category,
                    'message' => "Iteración número $counter terminada. Se encontraron $payoutsCount payouts",
                ]);

                $counter++;
                sleep(1);
            } catch(Exception $e) {
                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'error',
                    'category' => $log_category,
                    'message' => $e->getMessage(),
                    'exception' => $e
                ]);
            }
        } while ($hasMore && $startingAfter);

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => 'Se terminó el recorrido de paginación de stripe',
        ]);

        $allPayoutIds = array_reverse($allPayoutIds);
        $allPayoutIdsCount = sizeof($allPayoutIds);
        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Se encontraron en total: $allPayoutIdsCount payouts",
        ]);

        $existingCodes = StripePayments::whereIn('code', $allPayoutIds)->pluck('code')->toArray();
        $allPayoutIds = array_diff($allPayoutIds, $existingCodes);

        $data = array_map(fn($id) => [
            'code' => $id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ], $allPayoutIds);

        $allPayoutIdsCount = sizeof($allPayoutIds);

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Proceso terminado. Se insertaron $allPayoutIdsCount registros",
        ]);

        StripePayments::insert($data);
    }

    public function conciliatePayments($payouts, $process_id = null) {
        $log_category = 'conciliatePayments';
        $new_conciliated_payouts_count = 0;

        foreach($payouts as $payout) {
            try {
                if( !in_array($payout['status'], ['paid', 'pending', 'in_transit']) ) {
                    $payoutId = $payout['id'];
                    $payoutStatus = $payout['status'];
                    $this->createLog([
                        'process_id' => $process_id,
                        'type' => 'warning',
                        'category' => $log_category,
                        'message' => "Se encontró un payout con status $payoutStatus. payout_id: $payoutId"
                    ]);
                    continue;
                }

                $conciliation = $this->getBalancePayoutV2($payout['id']);
                $conciliationData = $conciliation->getData(true);
    
                foreach ($conciliationData['data'] as $balanceTransaction) {
                    try {
                        if($balanceTransaction['status'] !== 'available') continue; // Crear log
        
                        $payment = null;
                        if($balanceTransaction['source']['id'] ?? false) {
                            $payment = Payment::where("payment_method", "STRIPE")
                            ->where("reference", $balanceTransaction['source']['id'])
                            ->first();
                        }
                        if( !$payment ) continue;
        
                        $payment->reference_conciliation = $payout['id'];
                        $payment->date_conciliation = Carbon::parse($balanceTransaction['available_on'])->format('Y-m-d H:i:s'); // Se refiere a la fecha de cobro en la cuneta de stripe (por lo tanto balance_transaction)
                        $payment->amount = round($balanceTransaction['amount'] / 100, 2); // Cantidad bruta que le pertenece a este pago
                        $payment->total_fee = round($balanceTransaction['fee'] / 100, 2);
                        $payment->total_net = round($balanceTransaction['net'] / 100, 2); // Antes de ser depositado al banco
                        $payment->bank_name = $payout['destination']['bank_name'] ?? null;
                        if($payout['status'] === 'paid') {
                            $payment->is_conciliated = 1;
                            $payment->deposit_date = Carbon::parse($payout['arrival_date'])->format('Y-m-d H:i:s'); // Fecha en la que el pago cayó a la cuenta bancaria
                            $payment->total_final_net = round($balanceTransaction['net'] / 100, 2); // Después de ser depositado al banco
        
                            if( !StripePayments::where('code', $payout['id'])->exists() ) {
                                $stripePayment = new StripePayments();
                                $stripePayment->code = $payout['id'];
                                $stripePayment->save();

                                $new_conciliated_payouts_count++;
                            }
                        }
                        $payment->save();
                    } catch(Exception $e) {
                        $jsonStringified = json_encode($payout);
                        $this->createLog([
                            'process_id' => $process_id,
                            'type' => 'error',
                            'category' => $log_category,
                            'message' => $e->getMessage() . "------ $jsonStringified",
                            'exception' => $e
                        ]);
                    }
                }

                sleep(1);
            } catch(Exception $e) {
                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'error',
                    'category' => $log_category,
                    'message' => $e->getMessage() . " ---- payout_id: " . ($payout['id'] ?? 'Sin id'),
                    'exception' => $e
                ]);
            }
        }

        return $new_conciliated_payouts_count;
    }

    public function initializeAllRefunds() {
        $process_id = $this->generateRandomProcessId();
        $log_category = 'initializeAllRefunds';
        $paginationCounter = 0;

        $allRefunds = [];
        $startingAfter = null;

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Comenzando proceso de $log_category",
        ]);
        
        do {
            try {
                $response = $this->getRefunds($startingAfter);
                $decoded = $response->getData(true);
                
                $refunds = $decoded['data'] ?? [];
                $refoundsCount = sizeof($refunds);
                $allRefunds = array_merge($allRefunds, $refunds);
    
                $hasMore = $decoded['has_more'] ?? false;
                $startingAfter = !empty($refunds) ? end($refunds)['id'] : null;

                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'info',
                    'category' => $log_category,
                    'message' => "Iteración número $paginationCounter terminada. Se encontraron $refoundsCount devoluciones",
                ]);

                $paginationCounter++;
                sleep(1);
            } catch(Exception $e) {
                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'error',
                    'category' => $log_category,
                    'message' => $e->getMessage(),
                    'exception' => $e
                ]);
            }
        } while ($hasMore && $startingAfter);

        $refundsCount = sizeof($allRefunds);
        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Se terminaron de obtener todos los refunds de stripe. Se encontraron: $refundsCount",
        ]);

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Iniciando proceso de captura de reembolsos",
        ]);

        $allRefunds = array_reverse($allRefunds);
        $process_result = $this->initializeRefundsIfTheyDontExist($allRefunds, $process_id, $log_category);

        $payment_refunds_to_save_count = $process_result['payment_refunds_to_save_count'];
        $reservation_refunds_to_save_count = $process_result['reservation_refunds_to_save_count'];
        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Se terminó el proceso $log_category. payment_refunds_saved_count: $payment_refunds_to_save_count, reservation_refunds_saved_count: $reservation_refunds_to_save_count",
        ]);

        return $process_result;
    }

    public function initializeRefundsIfTheyDontExist($refunds, $process_id = null, $log_category = null) {
        $refunds_that_need_revision = [];
        $refunds_without_payment_found = [];
        $payment_refunds_to_save = [];
        $reservation_refunds_to_save = [];
        $process_counter = 0;

        foreach($refunds as $refund) {
            try {
                if($refund['status'] !== 'succeeded') continue;

                $payment = null;
                $payment_refund = null;

                // tratar de obtener pago original mediante id de cargo ch_...
                if($refund['charge']['id'] ?? false) {
                    $payment = Payment::where("payment_method", "STRIPE")
                    ->where("reference", $refund['charge']['id'])
                    ->first();
                }

                // tratar de obtener pago original mediante id de payment_intent pi_...
                if(!$payment && ($refund['payment_intent']['id'] ?? false)) {
                    $payment = Payment::where("payment_method", "STRIPE")
                    ->where("reference", $refund['payment_intent']['id'])
                    ->first();
                }
                if(!$payment) {
                    $refunds_without_payment_found[] = $refund;
                    continue;
                }

                $reservation = Reservation::find($payment->reservation_id);
                if(!$reservation) {
                    $this->createLog([
                        'process_id' => $process_id,
                        'type' => 'warning',
                        'category' => $log_category,
                        'message' => 'Se encontró un pago sin reservación relacionada',
                    ]);
                    continue;
                }

                // tratar de obtener la devolución mediante id de refund re_...
                $payment_refund = Payment::where("payment_method", "STRIPE")
                ->where("reference", $refund['id'])
                ->first();

                if( $payment_refund ) {
                    // Si ya se encontró el pago de devolución, se verifica si existe su reservation_refund
                    if($payment_refund->reservation_refund_id) continue; // Si lo encuentra significa que la data ya está existente, por lo que no se tiene que hacer nada más
                    
                    $reservation_refund = new ReservationsRefund();
                    $reservation_refund->reservation_id = $payment->reservation_id;
                    $reservation_refund->message_refund = 'Reembolso obtenido automáticamente mediante el sistema';
                    $reservation_refund->status = 'REFUND_COMPLETED';
                    $reservation_refund->end_at = Carbon::parse($refund['balance_transaction']['available_on'] ?? null)->format('Y-m-d H:i:s');
                    $reservation_refund->save();
                    $reservation_refunds_to_save[] = $reservation_refund;
                    
                    $payment_refund->reservation_refund_id = $reservation_refund->id;
                    $payment_refund->save();
                    $payment_refunds_to_save[] = $payment_refund;
                }
                else {
                    // tratar de obtener el pago de devolución mediante la tabla reservation_refunds
                    // Este paso es opcional, sólo quizá para tener cuidado y revisar después
                    if($payment && $refund['balance_transaction']) {
                        $payment_refunds = Payment::where("payment_method", "STRIPE")
                        ->where("reservation_id", $payment->reservation_id)
                        ->where("total", "<", 0)
                        ->get();
    
                        if($payment_refunds->count() > 0) {
                            $refunds_that_need_revision[] = $refund;
                            $refund_id = $refund['id'];

                            $this->createLog([
                                'process_id' => $process_id,
                                'type' => 'warning',
                                'category' => $log_category,
                                'message' => "Se encontraron devoluciones ya creadas para el payment $payment->id, pero no se encontró directamente su id de stripe $refund_id",
                            ]);
                            continue;
                        }
                    }

                    $reservation_refund = new ReservationsRefund();
                    $reservation_refund->reservation_id = $payment->reservation_id;
                    $reservation_refund->message_refund = 'Reembolso obtenido automáticamente mediante el sistema';
                    $reservation_refund->status = 'REFUND_COMPLETED';
                    $reservation_refund->end_at = Carbon::parse($refund['balance_transaction']['available_on'] ?? null)->format('Y-m-d H:i:s');
                    $reservation_refund->created_at = Carbon::parse($refund['created'])->format('Y-m-d H:i:s');
                    $reservation_refund->save();
                    $reservation_refunds_to_save[] = $reservation_refund;

                    $amount = null;
                    $total_fee = null;
                    $total_net = null;
                    if($refund['balance_transaction'] ?? false) {
                        $amount = round($refund['balance_transaction']['amount'] / 100, 2);
                        $total_fee = round($refund['balance_transaction']['fee'] / 100, 2);
                        $total_net = round($refund['balance_transaction']['net'] / 100, 2);
                    }

                    $payment_refund = new Payment();
                    $payment_refund->description = 'Stripe';
                    $payment_refund->total = round(($refund['balance_transaction']['amount'] ?? 0) / 100, 2);
                    $payment_refund->status = 1;
                    $payment_refund->currency = $refund['balance_transaction']['currency'] === 'mxn' ? 'MXN' : 'USD';
                    if($payment_refund->currency === 'MXN') {
                        if(strtolower($reservation->currency) === 'mxn') {
                            $payment_refund->exchange_rate = 1;
                            $payment_refund->operation = 'multiplication';
                        }
                        else {
                            $payment_refund->exchange_rate = $payment->exchange_rate;
                            $payment_refund->operation = 'division';
                        }
                    }
                    else {
                        if(strtolower($reservation->currency) === 'mxn') {
                            $payment_refund->exchange_rate = $payment->exchange_rate;
                            $payment_refund->operation = 'multiplication';
                        }
                        else {
                            $payment_refund->exchange_rate = 1;
                            $payment_refund->operation = 'multiplication';
                        }
                    }
                    $payment_refund->payment_method = 'STRIPE';
                    $payment_refund->object = null;
                    $payment_refund->reservation_id = $payment->reservation_id;
                    $payment_refund->reference = $refund['id'];
                    $payment_refund->reservation_refund_id = $reservation_refund->id;
                    $payment_refund->created_at = Carbon::parse($refund['created'])->format('Y-m-d H:i:s');
                    $payment_refund->is_conciliated = 0;
                    $payment_refund->is_refund = 1;
                    $payment_refund->refunded = 1;
                    $payment_refund->date_conciliation = Carbon::parse($refund['balance_transaction']['available_on'] ?? null)->format('Y-m-d H:i:s');
                    $payment_refund->deposit_date = null;
                    $payment_refund->amount = $amount;
                    $payment_refund->total_fee = $total_fee;
                    $payment_refund->total_net = $total_net;
                    $payment_refund->bank_name = null;
                    $payment_refund->conciliation_comment = null;
                    $payment_refund->category = 'REFUND';
                    $payment_refund->save();
                    $payment_refunds_to_save[] = $payment_refund;
                }
            } catch(Exception $e) {
                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'error',
                    'category' => $log_category,
                    'message' => $e->getMessage(),
                    'exception' => $e
                ]);
            }

            if($process_counter > 10) {
                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'info',
                    'category' => $log_category,
                    'message' => "Se han procesado $process_counter iteraciones",
                ]);
                $process_counter = 0;
            }
            $process_counter++;
        }

        return [
            'refunds_that_need_revision_count' => sizeof($refunds_that_need_revision),
            'refunds_without_payment_found_count' => sizeof($refunds_without_payment_found),
            'payment_refunds_to_save_count' => sizeof($payment_refunds_to_save),
            'reservation_refunds_to_save_count' => sizeof($reservation_refunds_to_save),

            'refunds_that_need_revision' => $refunds_that_need_revision,
            'refunds_without_payment_found' => $refunds_without_payment_found,
            'payment_refunds_to_save' => $payment_refunds_to_save,
            'reservation_refunds_to_save' => $reservation_refunds_to_save,
        ];
    }

    public function initializeAllPaymentsConciliation() {
        $process_id = $this->generateRandomProcessId();
        $log_category = 'initializeAllPaymentsConciliation';

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Iniciando proceso $log_category",
        ]);

        StripePayments::truncate();
        $this->initializeAllPayoutsInDb();

        $stripe_payments = StripePayments::get();
        $payouts_count = $stripe_payments->count();
        $counter = 0;
        $total_counter = 0;

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Total de payouts a procesar: $payouts_count. Iniciando recorrido",
        ]);

        foreach($stripe_payments as $stripe_payment) {
            $response = $this->getPayoutV2($stripe_payment->code);
            $payout = $response->getData(true);

            $this->conciliatePayments([$payout], $process_id);
            
            $counter++;
            $total_counter++;
            if($counter > 4) {
                $this->createLog([
                    'process_id' => $process_id,
                    'type' => 'info',
                    'category' => $log_category,
                    'message' => "Se han procesado $total_counter de $payouts_count payouts. Último payout: $stripe_payment->code",
                ]);
                $counter = 0;
            }

            sleep(1);
        }

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Proceso terminado exitosamente",
        ]);
    }

    public function checkForNewRefunds() {
        $process_id = $this->generateRandomProcessId();
        $log_category = 'checkForNewRefunds';

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Comenzando proceso de $log_category",
        ]);

        $response = $this->getRefunds();
        $decoded = $response->getData(true);
        $refunds = array_reverse($decoded['data'] ?? []);

        $result = $this->initializeRefundsIfTheyDontExist($refunds);
        $payment_refunds_to_save_count = $result['payment_refunds_to_save_count'];

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Proceso terminado, se registraron: $payment_refunds_to_save_count nuevas devoluciones",
        ]);
    }

    public function checkForNewConciliations() {
        $process_id = $this->generateRandomProcessId();
        $log_category = 'checkForNewConciliations';

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Comenzando proceso de $log_category",
        ]);

        $response = $this->getPayouts(null, 10);
        $decoded = $response->getData(true);
        $payouts = array_reverse($decoded['data'] ?? []);

        $payoutIds = collect($payouts)->pluck('id');
        $existingPayoutIds = StripePayments::whereIn('code', $payoutIds)->pluck('code');

        $payoutsToConciliate = collect($payouts)->filter(function ($payout) use ($existingPayoutIds) {
            return !$existingPayoutIds->contains($payout['id']);
        })->values()->all();

        $new_conciliated_payouts_count = $this->conciliatePayments($payoutsToConciliate, $process_id);

        $this->createLog([
            'process_id' => $process_id,
            'type' => 'info',
            'category' => $log_category,
            'message' => "Proceso terminado, se registraron: $new_conciliated_payouts_count nuevas conciliaciones",
        ]);
    }

    private function generateRandomProcessId() {
        $length = 32;
        return bin2hex(random_bytes($length / 2));
    }
}