<?php

namespace App\Repositories\Operation;

use Illuminate\Http\Response;
use Carbon\Carbon;
use Exception;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class QuotationRepository
{
    use FiltersTrait, QueryTrait;

    public function get($request)
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $dates = isset($request->date) && !empty($request->date) 
        ? explode(" - ", $request->date)
        : [$start->toDateString(), $end->toDateString()];

        $dataUser = auth()->user();
        $userId = $dataUser->id; // Obtener ID del usuario autenticado

        //Para las ventas velidamos que su estatus de reserva sea CONFIRMADO, CREDITO O CREDITO ABIERTO
        $paramBookingStatus = $this->parseArrayQuery(['CONFIRMED', 'CREDIT', 'OPENCREDIT'], "single");
        $queryHavingBooking = " HAVING reservation_status IN ($paramBookingStatus) ";

        $query = " AND rez.site_id != 21 AND rez.site_id != 11 
                   AND rez.created_at BETWEEN :init AND :end
                   AND rez.is_duplicated = 0
                   AND us.id = :user ";

        $queryData = [
            'init' => "{$dates[0]} 00:00:00",
            'end' => "{$dates[1]} 23:59:59",
            'user' => $userId,
        ];

        $bookings = $this->queryBookings($query, $queryHavingBooking, $queryData);

        return view('management.quotation.view', [ "items" => $bookings ]);
    }
}