<?php

namespace App\Repositories\Reservations;

use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\ReservationsService;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReservationsRepository
{
    public function index($request)
    {
        if(!$request->lookup_date){
            $from = date('Y-m-d');
            $to = date('Y-m-d');

            $services = ReservationsService::select('reservation_item_id')->whereDate('pickup', '>=', $from)
                ->whereDate('pickup', '<=', $to)
                ->pluck('reservation_item_id');
            $items = ReservationsItem::select('reservation_id')->whereIn('id', $services)->pluck('reservation_id');
            $reservations = Reservation::whereIn('id',$items)->with('destination', 'items')->get();
            return view('reservations.index', compact('reservations','from','to'));
        }else{
            if(strlen($request->lookup_date) <= 10){
                $from = $request->lookup_date;
                $to = $request->lookup_date;
            }else{
                $dates = explode(' a ', $request->lookup_date);
                $from = $dates[0];
                $to = $dates[1];
            }          

            $services = ReservationsService::select('reservation_item_id')->whereDate('pickup', '>=', $from)
                ->whereDate('pickup', '<=', $to)
                ->pluck('reservation_item_id');
            $items = ReservationsItem::select('reservation_id')->whereIn('id', $services)->pluck('reservation_id');
            $reservations = Reservation::whereIn('id',$items)->with('destination', 'items')->get();
            return view('reservations.index', compact('reservations','from','to'));
        }        
    }
}