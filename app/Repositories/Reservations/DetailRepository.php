<?php

namespace App\Repositories\Reservations;

use App\Models\DestinationService;
use App\Models\Reservation;
use App\Models\SalesType;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Site;
use App\Models\Zones;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DetailRepository
{
    public function detail($request,$id)
    {
        $reservation = Reservation::with('destination','items','sales', 'callCenterAgent','payments','followUps','site')->find($id);
        $users_ids = UserRole::where('role_id', 3)->orWhere('role_id',4)->pluck('user_id');
        $sellers = User::whereIn('id', $users_ids)->get();
        
        $sales_types = SalesType::all();
        $services_types = DestinationService::where('status',1)->where('destination_id',$reservation->destination_id)->get();
        $zones = Zones::where('destination_id', 1)->get();
        $sites = Site::get();

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

        // return $reservation;

        return view('reservations.detail', compact('reservation','sellers','sales_types','services_types','data','sites','zones'));
    }
}