<?php

namespace App\Repositories\Reports;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;

use App\Models\Reservation;
use App\Models\ReservationFollowUp;

//TRAIT
use App\Traits\MethodsTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class CashRepository
{
    use MethodsTrait, FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        // Función auxiliar para obtener fechas seguras
        $dates = MethodsTrait::parseDateRange($request->date ?? '');

        $data = [
            "init" => $dates['init'],
            "end" => $dates['end'],
            "filter_text" => ( isset( $request->filter_text ) && !empty( $request->filter_text ) ? $request->filter_text : NULL ),

            "currency" => ( isset($request->currency) ? $request->currency : 0 ),
            "service_operation_status" => ( isset($request->service_operation_status) ? $request->service_operation_status : 0 ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => $dates['init'] . " 00:00:00",
            'end' => $dates['end'] . " 23:59:59",
        ];

        //MONEDA DE LA RESERVA
        if(isset( $request->currency ) && !empty( $request->currency )){
            $params = $this->parseArrayQuery($request->currency,"single");
            $queryOne .= " AND rez.currency IN ($params) ";
            $queryTwo .= " AND rez.currency IN ($params) ";
        }

        //ESTATUS DE RESERVACIÓN
        $params = $this->parseArrayQuery(['CREDIT','PENDING','PAY_AT_ARRIVAL','CONFIRMED'],"single");
        $havingConditions[] = " reservation_status IN (".$params.") ";

        //ESTATUS DE SERVICIO
        if(isset( $request->service_operation_status ) && !empty( $request->service_operation_status )){
            $params = $this->parseArrayQuery($request->service_operation_status,"single");
            $queryOne .= " AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND it.op_two_status IN ($params) ";
        }else{
            $params = $this->parseArrayQuery(['COMPLETED'],"single");
            $queryOne .= " AND it.op_one_status IN ($params) ";
            $queryTwo .= " AND it.op_two_status IN ($params) ";            
        }

        //METODO DE PAGO
        $paramsPayment = "FIND_IN_SET('CASH', payment_type_name) > 0 OR ";
        $paramsPayment = rtrim($paramsPayment, ' OR ');
        $havingConditions[] = " (".$paramsPayment.") ";

        if(isset( $request->filter_text ) && !empty( $request->filter_text )){
            $queryData = [];
            $queryOne  .= " AND (
                        ( CONCAT(rez.client_first_name,' ',rez.client_last_name) like '%".$data['filter_text']."%') OR
                        ( rez.client_phone like '%".$data['filter_text']."%') OR
                        ( rez.client_email like '%".$data['filter_text']."%') OR
                        ( rez.reference like '%".$data['filter_text']."%') OR
                        ( it.code like '%".$data['filter_text']."%' )
                    )";

            $queryTwo  .= " AND (
                        ( CONCAT(rez.client_first_name,' ',rez.client_last_name) like '%".$data['filter_text']."%') OR
                        ( rez.client_phone like '%".$data['filter_text']."%') OR
                        ( rez.client_email like '%".$data['filter_text']."%') OR
                        ( rez.reference like '%".$data['filter_text']."%') OR
                        ( it.code like '%".$data['filter_text']."%' )
                    )";                    
        }

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