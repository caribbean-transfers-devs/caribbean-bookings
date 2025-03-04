<?php

namespace App\Repositories\Bots;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

//MODELS
use App\Models\ReservationsItem;
use App\Models\Sale;
use App\Models\Payment;

//TRAITS
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class MasterToursRepository
{
    use FiltersTrait, QueryTrait;

    public function __construct()
    {
        
    }

    public function ListServicesMasterTour($request)
    {
        try {
            // dump(400, round((400 / 19 ),2));
            // dump(550, round((550 / 19 ),2));
            // dump(650, round((650 / 19 ),2));
            // dump(700, round((700 / 19 ),2));
            // dump(750, round((750 / 19 ),2));
            // dump(800, round((800 / 19 ),2));
            // dump(850, round((850 / 19 ),2));
            // dump(950, round((950 / 19 ),2));
            // dump(1000, round((1000 / 19 ),2));
            // dump(1100, round((1100 / 19 ),2));
            // dump(1200, round((1200 / 19 ),2));
            // dump(1300, round((1300 / 19 ),2));
            // dump(1500, round((1500 / 19 ),2));
            // dump(1800, round((1800 / 19 ),2));
            // dump(2000, round((2000 / 19 ),2));
            // dump(2200, round((2200 / 19 ),2));
            // dump(2500, round((2500 / 19 ),2));
            // dump(3000, round((3000 / 19 ),2));
            // dump(3500, round((3500 / 19 ),2));
            // dump(3800, round((3800 / 19 ),2));
            // dump(4000, round((4000 / 19 ),2));
            // dump(4500, round((4500 / 19 ),2));
            // dump(7000, round((7000 / 19 ),2));

            ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el límite a 60 segundos
    
            $dates = isset($request->date) && !empty($request->date) 
            ? explode(" - ", $request->date)
            : [date('Y-m-d'), date('Y-m-d')];

            if (count($dates) < 2) {
                throw new \Exception("El formato de fecha recibido no es válido.");
            }            
    
            // Condiciones de Reservas
            $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two 
                          AND rez.is_cancelled = 0 
                          AND rez.is_duplicated = 0 
                          AND rez.open_credit = 0 
                          AND rez.is_quotation = 0 ";
    
            $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four 
                          AND rez.is_cancelled = 0 
                          AND rez.is_duplicated = 0 
                          AND rez.open_credit = 0 
                          AND rez.is_quotation = 0 
                          AND it.is_round_trip = 1 ";
    
            //SITIO
            $params = $this->parseArrayQuery([30],"single");
            $queryOne .= " AND site.id IN ($params) ";
            $queryTwo .= " AND site.id IN ($params) ";                   
    
            $queryData = [
                'init' => "{$dates[0]} 00:00:00",
                'end' => "{$dates[1]} 23:59:59",
            ];
    
            // Obtener operaciones
            $operations = $this->queryOperations($queryOne, $queryTwo, '', $queryData);
            if (!$operations) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontraron datos.',
                    'data' => [],
                ], Response::HTTP_OK);
            }

            DB::beginTransaction();
            
            $sales = [];
            if( $operations ){
                foreach ($operations as $operation) {
                    if( $operation->total_sales == 0 ){
                        $rate = $this->Rate( $operation->service_type_id, $operation->passengers, $operation->zone_one_id, $operation->zone_two_id );
                        $sale = Sale::where('reservation_id', $operation->reservation_id)->first();
                        // dd($rate, $sale);
                        if ($sale) {
                            $sale->total = $rate["amount"];
                            if( $sale->save() ){
                                if( $operation->op_one_operating_cost == 0 ){
                                    $item = ReservationsItem::find($operation->id);
                                    if ($item) {
                                        $item->op_one_operating_cost = round( ($rate['operating_cost'] * 19), 2 );
                                        $item->save();
                                    }
                                }
                            }
                            $sales[] = $sale;
                        }
                    }
                }
            }

            DB::commit(); // Se confirma la transacción
    
            return response()->json([
                'success' => true,
                'message' => 'se encontraron datos',
                'data' => $sales,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Error en ListServicesMasterTour: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar la solicitud.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function Rate(int $vehicle = 1, int $pax = 8, int $zone_one = 1, int $zone_two = 1):array
    {
        //return
        $data = [
            "amount" => 0,
            "operating_cost" => 0
        ];

        // params query
        $params = [
            "destination_id" => 1,
            "destination_service_id" => $vehicle,
            "zone_one" => $zone_one,
            "zone_two" => $zone_two,
            "zone_three" => $zone_two,
            "zone_four" => $zone_one,
            "enterprise_id" => 11,
        ];        

        $rates = DB::select("SELECT 
                                    ds.name as service_name, ds.price_type,
                                    rt.*,
                                    zoneOne.name as from_name,
	                                zoneTwo.name as to_name
                             FROM rates_enterprises as rt
                                    LEFT JOIN destination_services as ds ON ds.id = rt.destination_service_id
                                    LEFT JOIN enterprises as e ON e.id = rt.enterprise_id
                                    LEFT JOIN zones as zoneOne ON zoneOne.id = rt.zone_one
                                    LEFT JOIN zones as zoneTwo ON zoneTwo.id = rt.zone_one
                             WHERE rt.destination_id = :destination_id
                                AND rt.destination_service_id = :destination_service_id
                                AND ( (rt.zone_one = :zone_one AND rt.zone_two = :zone_two) OR ( rt.zone_one = :zone_three AND rt.zone_two = :zone_four )  ) 
                                AND e.id = :enterprise_id", $params);

        // $rates = DB::select("SELECT 
        //                             ds.name as service_name, 
        //                             ds.price_type, 
        //                             rt.*, 
        //                             zoneOne.name as from_name, 
        //                             zoneTwo.name as to_name
        //                         FROM (
        //                             SELECT * FROM rates_enterprises 
        //                             WHERE destination_id = :destination_id
        //                             AND destination_service_id = :destination_service_id
        //                             AND (
        //                                 (zone_one = :zone_one AND zone_two = :zone_two) 
        //                                 OR (zone_one = :zone_three AND zone_two = :zone_four)
        //                             )
        //                             AND enterprise_id = :enterprise_id
        //                         ) as rt
        //                         LEFT JOIN destination_services as ds ON ds.id = rt.destination_service_id
        //                         LEFT JOIN enterprises as e ON e.id = rt.enterprise_id
        //                         LEFT JOIN zones as zoneOne ON zoneOne.id = rt.zone_one
        //                         LEFT JOIN zones as zoneTwo ON zoneTwo.id = rt.zone_two", $params);        

        if( $rates ){
            if( $vehicle == 1 || $vehicle == 3 || $vehicle == 6 ){
                $data['amount'] = ( $pax >= 8 ? $rates[0]->ow_12 : ( $pax >= 3 ? $rates[0]->ow_37 : $rates[0]->up_8_ow ) );
            }else{
                $data['amount'] = $rates[0]->one_way;
            }
            $data['operating_cost'] = $rates[0]->operating_cost;
        }

        return $data;
    }    
}