<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class DataTransfersController extends Controller
{
    public function dataTranfers(Request $request){
        $reservations = DB::connection('origen')
                            ->table('reservations')
                            ->whereBetween('created_at', ['2024-05-01 00:00:00', '2024-05-01 23:59:59'])
                            ->where('site_id', 21)
                            ->get();

        if( !empty($reservations) ){
            foreach ($reservations as $key => $reservation) {                
                $reservations_items = DB::connection('origen')
                                        ->table('reservations_items')
                                        ->where('reservation_id', $reservation->id)
                                        ->get();

                $reservations_follow = DB::connection('origen')
                                        ->table('reservations_follow_up')
                                        ->where('reservation_id', $reservation->id)
                                        ->get();

                $sales = DB::connection('origen')
                                        ->table('sales')
                                        ->where('reservation_id', $reservation->id)
                                        ->get();

                $payments = DB::connection('origen')
                                        ->table('payments')
                                        ->where('reservation_id', $reservation->id)
                                        ->get();

                
                $booking = DB::connection('destino')->table('reservations')->insertGetId([
                                                                                        'client_first_name' => $reservation->client_first_name,
                                                                                        'client_last_name' => $reservation->client_last_name,
                                                                                        'client_email' => $reservation->client_email,
                                                                                        'client_phone' => $reservation->client_phone,
                                                                                        'currency' => $reservation->currency,
                                                                                        'language' => $reservation->language,
                                                                                        'rate_group' => $reservation->rate_group,
                                                                                        'is_cancelled' => $reservation->is_cancelled,
                                                                                        'is_commissionable' => $reservation->is_commissionable,
                                                                                        'pay_at_arrival' => $reservation->pay_at_arrival,
                                                                                        'site_id' => $reservation->site_id,
                                                                                        'destination_id' => $reservation->destination_id,
                                                                                        'created_at' => $reservation->created_at,
                                                                                        'updated_at' => $reservation->updated_at,
                                                                                        'reference' => $reservation->reference,
                                                                                        'affiliate_id' => $reservation->affiliate_id,
                                                                                        'payment_reconciled' => $reservation->payment_reconciled,
                                                                                        'vendor_id' => $reservation->vendor_id,
                                                                                        'user_id' => $reservation->user_id,
                                                                                        'accept_messages' => $reservation->accept_messages,
                                                                                        'terminal' => $reservation->terminal,
                                                                                        'is_duplicated' => $reservation->is_duplicated,
                                                                                        'cancellation_type_id' => $reservation->cancellation_type_id,
                                                                                        'comments' => $reservation->comments,
                                                                                  ]);

                if( !empty($reservations_items) ){
                    foreach($reservations_items as $reservations_item){
                        DB::connection('destino')->table('reservations_items')->insert([
                            'reservation_id' => $booking,
                            'code' => $reservations_item->code,
                            'destination_service_id' => $reservations_item->destination_service_id,
                            'from_name' => $reservations_item->from_name,
                            'from_lat' => $reservations_item->from_lat,
                            'from_lng' => $reservations_item->from_lng,
                            'from_zone' => $reservations_item->from_zone,
                            'to_name' => $reservations_item->to_name,
                            'to_lat' => $reservations_item->to_lat,
                            'to_lng' => $reservations_item->to_lng,
                            'to_zone' => $reservations_item->to_zone,
                            'distance_time' => $reservations_item->distance_time,
                            'distance_km' => $reservations_item->distance_km,
                            'is_round_trip' => $reservations_item->is_round_trip,
                            'flight_number' => $reservations_item->flight_number,
                            'flight_data' => $reservations_item->flight_data,
                            'passengers' => $reservations_item->passengers,
                            'op_one_status' => $reservations_item->op_one_status,
                            'op_one_pickup' => $reservations_item->op_one_pickup,
                            'op_one_confirmation' => $reservations_item->op_one_confirmation,
                            'op_two_status' => $reservations_item->op_two_status,
                            'op_two_pickup' => $reservations_item->op_two_pickup,
                            'op_two_confirmation' => $reservations_item->op_two_confirmation,
                            'created_at' => $reservations_item->created_at,
                            'updated_at' => $reservations_item->updated_at,
                            'spam' => $reservations_item->spam,
                      ]);                        
                    }
                }

                if( !empty($reservations_follow) ){
                    foreach($reservations_follow as $reservation_follow){
                        DB::connection('destino')->table('reservations_follow_up')->insert([
                            'reservation_id' => $booking,
                            'name' => $reservation_follow->name,
                            'text' => $reservation_follow->text,
                            'type' => $reservation_follow->type,
                            'created_at' => $reservation_follow->created_at,
                            'updated_at' => $reservation_follow->updated_at,
                      ]);                        
                    }
                }
                
                if( !empty($sales) ){
                    foreach($sales as $sale){
                        DB::connection('destino')->table('sales')->insert([
                            'reservation_id' => $booking,
                            'sale_type_id' => $sale->sale_type_id,
                            'description' => $sale->description,
                            'quantity' => $sale->quantity,
                            'total' => $sale->total,
                            'call_center_agent_id' => $sale->call_center_agent_id,
                            'created_at' => $sale->created_at,
                            'updated_at' => $sale->updated_at,
                            'deleted_at' => $sale->deleted_at,
                      ]);                        
                    }
                }

                if( !empty($payments) ){
                    foreach($payments as $payment){
                        DB::connection('destino')->table('payments')->insert([
                            'description' => $payment->description,
                            'total' => $payment->total,
                            'exchange_rate' => $payment->exchange_rate,
                            'status' => $payment->status,
                            'operation' => $payment->operation,
                            'payment_method' => $payment->payment_method,
                            'currency' => $payment->currency,
                            'object' => $payment->object,
                            'reservation_id' => $booking,
                            'reference' => $payment->reference,
                            'created_at' => $payment->created_at,
                            'updated_at' => $payment->updated_at,
                            'deleted_at' => $payment->deleted_at,
                            'user_id' => $payment->user_id,
                            'clip_id' => $payment->clip_id,
                      ]);                        
                    }
                }
                              
                // dump($booking);
            }
        }
    }
}
