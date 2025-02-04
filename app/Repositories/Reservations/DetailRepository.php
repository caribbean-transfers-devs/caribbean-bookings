<?php

namespace App\Repositories\Reservations;

use App\Models\DestinationService;
use App\Models\Reservation;
use App\Models\SalesType;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Site;
use App\Models\Zones;

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
            'destination',
            'items.cancellationTypeOrigin',
            'items.cancellationTypeDestino',
            'sales.callCenterAgent',  // Mantienes la relación con ventas por si necesitas la información de ventas // Relación anidada
            'callCenterAgent',  // Relación directa con el agente del call center
            'payments',
            'followUps',
            'site',
            'cancellationType',
            'originSale'
        ])->find($id);

        // dd($reservation->toArray());
                
        $users_ids = UserRole::where('role_id', 3)->orWhere('role_id',4)->pluck('user_id');
        $sellers = User::whereIn('id', $users_ids)->get();        
        $sales_types = SalesType::all();
        $services_types = DestinationService::where('destination_id',$reservation->destination_id)->get();
        $zones = Zones::where('destination_id', 1)->get();
        $sites = Site::get();
        $types_cancellations = ApiTrait::makeTypesCancellations();
        $media = ReservationsMedia::orderBy('id', 'desc')->get();
        
        //Sumamos las ventas y restamos pagos para saber si la reserva está confirmada o no..
        $data = [
            "status" => "PENDING",
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

        if( round( $data['total_payments'], 2) >= round( $data['total_sales'], 2) ):
            $data['status'] = "CONFIRMED";
        endif;
        if($reservation->is_cancelled == 1):
            $data['status'] = "CANCELLED";
        endif;
        if($reservation->is_duplicated == 1):
            $data['status'] = "DUPLICATED";
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
            'sellers' => $sellers,
            'sales_types' => $sales_types,
            'services_types' => $services_types,
            'data' => $data,
            'sites' => $sites,
            'zones' => $zones,
            'types_cancellations' => $types_cancellations,
            'media' => $media,
            'origins' => $this->Origins(),
            'request' => $request->input(),
            'data_user' => auth()->user()
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