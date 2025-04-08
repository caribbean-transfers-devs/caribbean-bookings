<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;

use App\Models\Reservation;
use App\Models\ReservationFollowUp;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class CashRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[0] : date("Y-m-d") ) . " 00:00:00",
            'end' => ( isset( $request->date ) && !empty( $request->date) ? explode(" - ", $request->date)[1] : date("Y-m-d") ) . " 23:59:59",
        ];

        $params = $this->parseArrayQuery(['CREDIT','PENDING','PAY_AT_ARRIVAL','CONFIRMED'],"single");
        $havingConditions[] = " reservation_status IN (".$params.") ";

        $paramsPayment = "FIND_IN_SET('CASH', payment_type_name) > 0 OR ";
        $paramsPayment = rtrim($paramsPayment, ' OR ');
        $havingConditions[] = " (".$paramsPayment.") ";

        if( !empty($havingConditions) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        // dd($queryOne, $queryTwo, $queryHaving, $queryData);
        $operations = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        return view('reports.cash.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Reporte de efectivo del ". date("Y-m-d", strtotime($data['init'])) ." al ". date("Y-m-d", strtotime($data['end'])),
                    "active" => true
                ]
            ],
            'items' => $operations,
            'data' => $data,            
        ]);
    }

    public function update($request)
    {
        try {
            DB::beginTransaction();
            
            $item = Reservation::find($request->id);
            $item->payment_reconciled = $request->status;
            $item->save();            

            $follow_up_db = new ReservationFollowUp;
            $follow_up_db->name = auth()->user()->name;
            $follow_up_db->text = "El pago en efectivo se ha conciliado como (".(( $request->status == 1 )? 'Positivo' : 'Negativo' ).") por ". auth()->user()->name;
            $follow_up_db->type = 'HISTORY';
            $follow_up_db->reservation_id = $request->id;
            $follow_up_db->save();

            DB::commit();
            return response()->json(['message' => 'Estatus actualizado con éxito', 'success' => true], Response::HTTP_OK);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al actualizar el estatus'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}