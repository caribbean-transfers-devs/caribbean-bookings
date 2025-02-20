<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;

use App\Traits\ApiTrait;
use App\Traits\FiltersTrait;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsMedia;

class DetailRepository
{
    use ApiTrait, FiltersTrait;
    
    public function detail($request,$id)
    {
        $reservation = Reservation::with([
            'destination.destination_services',
            'items' => function ($query) {
                $query->join('zones as zone_one', 'zone_one.id', '=', 'reservations_items.from_zone')
                        ->join('zones as zone_two', 'zone_two.id', '=', 'reservations_items.to_zone')
                        ->select(
                            'reservations_items.*', 
                            'reservations_items.id as reservations_item_id', 
                            'zone_one.name as from_zone_name',
                            'zone_one.is_primary as is_primary_from',
                            'zone_two.name as to_zone_name',
                            'zone_two.is_primary as is_primary_to',
                            // Final Service Type para zone_one
                            DB::raw("
                                CASE 
                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type_one
                            "),
                            
                            // Final Service Type para zone_two
                            DB::raw("
                                CASE 
                                    WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                    ELSE 'ARRIVAL'
                                END AS final_service_type_two
                            ")
                        );
            },
            // 'items.cancellationTypeOrigin',
            // 'items.cancellationTypeDestino',
            'sales.callCenterAgent',  // Mantienes la relación con ventas por si necesitas la información de ventas // Relación anidada
            'callCenterAgent',  // Relación directa con el agente del call center
            'payments',
            'followUps',
            'site',
            'cancellationType',
            'originSale'
        ])->find($id);
        // dd($reservation->toArray());
;
        $types_cancellations = ApiTrait::makeTypesCancellations();
        
        //Sumamos las ventas y restamos pagos para saber si la reserva está confirmada o no..
        $data = [
            "status" => "PENDING", //NOS INDICA EL ESTATUS DEFAULT DE LA RESERVA
            "total_sales" => 0,
            "total_payments" => 0,
        ];

        foreach( $reservation->sales as $sale ):
            $data['total_sales'] += $sale->total;            
        endforeach;

        foreach( $reservation->payments as $payment ):
            if($payment->operation == "multiplication"):
                $data['total_payments'] += ($payment->total * $payment->exchange_rate);
            endif;
            if($payment->operation == "division"):
                $data['total_payments'] += ($payment->total / $payment->exchange_rate);
            endif;                      
        endforeach;

        if($reservation->is_cancelled == 1):
            $data['status'] = "CANCELLED";
        endif;
        if($reservation->is_duplicated == 1):
            $data['status'] = "DUPLICATED";
        endif;
        if($reservation->open_credit == 1):
            $data['status'] = "OPENCREDIT";
        endif;
        if($reservation->is_quotation == 1):
            $data['status'] = "QUOTATION";
        endif;        
        if( $reservation->site->is_cxc == 1 && round( $data['total_payments'], 2) == 0 ):
            $data['status'] = "CREDIT";
        endif;            
        if( round( $data['total_payments'], 2) != 0 && ( $reservation->is_cancelled == 0 && $reservation->is_duplicated == 0 && $reservation->open_credit == 0 ) && ( round( $data['total_payments'], 2) >= round( $data['total_sales'], 2) ) ):
            $data['status'] = "CONFIRMED";
        endif;

        return view('reservations.detail', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Detalle de reservación: ".$id,
                    "active" => true
                ]
            ],
            'reservation' => $reservation,
            'data' => $data,
            'types_cancellations' => $types_cancellations,
            'request' => $request->input(),
        ]);
    }

    public function getMedia($request){
        if( isset($request->type) ){
            $media = ReservationsMedia::where('reservation_id', $request->id)
                                        ->where('type_media', 'OPERATION')
                                        ->orderBy('id', 'desc')
                                        ->get();
        }else{
            $media = ReservationsMedia::where('reservation_id', $request->id)
                                        ->orderBy('id', 'desc')
                                        ->get();
        }

        return view('reservations.media', compact('media'));        
    }
}