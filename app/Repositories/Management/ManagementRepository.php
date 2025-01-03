<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class ManagementRepository
{
    use FiltersTrait, QueryTrait;

    public function confirmation($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? $request->date : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? $request->date : date("Y-m-d") ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.site_id NOT IN(21,11) ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.site_id NOT IN(21,11) ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? $request->date : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? $request->date : date("Y-m-d") ) . " 23:59:59",
        ];

        // dd($queryOne, $queryTwo, $queryHaving, $queryData);
        $confirmations = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        return view('management.confirmations', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de confirmaciones del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'confirmations' => $confirmations,
            'data' => $data,
        ]);
    }

    public function afterSales($request){
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ),            
        ];

        //SPAM
        $queryOneSpam = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND it.is_round_trip = 0 AND rez.site_id NOT IN(11,21,29) ";
        $queryTwoSpam = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND it.is_round_trip = 0 AND rez.site_id NOT IN(11,21,29) ";
        $havingConditions = []; $queryHaving = "";

        //RESERVACTIONS PENDING
        $queryBookings = ' AND rez.site_id NOT IN(11,21) AND rez.created_at BETWEEN :init AND :end ';
        $havingConditionsBooking = []; $queryHavingBooking = "";

        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];

        //ESTATUS DE RESERVACIÓN

        $params = $this->parseArrayQuery(['PENDING'],"single");
        $havingConditionsBooking[] = " reservation_status IN (".$params.") ";

        $queryHavingBooking = " HAVING " . implode(' AND ', $havingConditionsBooking);

        // dd($queryOneSpam, $queryTwoSpam, $queryHaving, $queryData);
        $spams = $this->queryOperations($queryOneSpam, $queryTwoSpam, $queryHaving, $queryData);

        // dd($queryBookings, $queryHaving, $queryData);
        $bookings = $this->queryBookings($queryBookings, $queryHavingBooking, $queryData);

        return view('management.aftersales', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de post venta del " . date("Y-m-d", strtotime($data['init'])) . " al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'spams' => $spams,
            'bookings' => $bookings,
            'data' => $data,
            'request' => $request->input()
        ]);        
    }
}