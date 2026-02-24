<?php

namespace App\Http\Controllers\Operations;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as ResponseFile;

//MODELS
use App\Models\Enterprise;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Reservation;
use App\Models\ReservationsItem;
use App\Models\Sale;
use App\Models\Zones;
use App\Models\Destination;
use App\Models\DestinationService;
use App\Models\DriverSchedule;
use App\Models\RatesGroup;
use App\Models\RatesTransfer;
use App\Models\RatesEnterprise;

//TRAIT
use App\Traits\ApiTrait;
use App\Traits\CodeTrait;
use App\Traits\RoleTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\FollowUpTrait;
use App\Traits\OperationTrait;

//EXCEPTION
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use DateTime;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class OperationsController extends Controller
{
    use ApiTrait, CodeTrait, RoleTrait, FiltersTrait, QueryTrait, FollowUpTrait, OperationTrait;

    public function index(Request $request){
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        $data = [
            "init" => ( isset( $request->date ) && !empty( $request->date) ? $request->date : date("Y-m-d") ),
            "end" => ( isset( $request->date ) && !empty( $request->date) ? $request->date : date("Y-m-d") ),
            "site" => ( isset($request->site) ? $request->site : 0 ),
            "reservation_status" => ( isset($request->reservation_status) ? $request->reservation_status : 0 ),
            "service_operation" => ( isset($request->service_operation) ? $request->service_operation : 0 ),
            "unit" => ( isset($request->unit) ? $request->unit : 0 ),
            "driver" => ( isset($request->driver) ? $request->driver : 0 ),
        ];

        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one   AND :init_date_two  AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND ( it.op_one_status != 'CANCELLED' OR  (it.op_one_status = 'CANCELLED' AND (it.op_one_cancellation_level = 'OPERATION' OR it.op_one_cancellation_level IS NULL)) ) ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND ( it.op_two_status != 'CANCELLED' OR  (it.op_two_status = 'CANCELLED' AND (it.op_two_cancellation_level = 'OPERATION' OR it.op_two_cancellation_level IS NULL)) ) AND it.is_round_trip = 1 ";        
        $havingConditions = []; $queryHaving = "";
        $date = ( isset( $request->date ) ? $request->date : date("Y-m-d") );
        $queryData = [
            'init' => $date." 00:00:00",
            'end' => $date." 23:59:59"
        ];

        //SITIO
        if( isset($request->site) && !empty($request->site) ){
            $params = $this->parseArrayQuery($request->site);
            $queryOne .= " AND site.id IN ($params) ";
            $queryTwo .= " AND site.id IN ($params) ";
        }

        //ESTATUS DE RESERVACIÓN
        if(isset( $request->reservation_status ) && !empty( $request->reservation_status )){
            $params = $this->parseArrayQuery($request->reservation_status,"single");
            $havingConditions[] = " reservation_status IN (".$params.") ";
        }

        //TIPO DE SERVICIO EN OPERACIÓN
        if(isset( $request->service_operation ) && !empty( $request->service_operation )){
            $params = $this->parseArrayQuery($request->service_operation,"single");
            $havingConditions[] = " final_service_type IN (".$params.") ";
        }

        //UNIDAD ASIGNADA AL SERVICIO
        if( isset($request->unit) && !empty($request->unit) ){
            $params = $this->parseArrayQuery($request->unit);
            $queryOne .= " AND it.vehicle_id_one IN ($params) ";
            $queryTwo .= " AND it.vehicle_id_two IN ($params) ";            
        }

        //CONDUCTOR ASIGNADO AL SERVICIO
        if( isset($request->driver) && !empty($request->driver) ){
            $params = $this->parseArrayQuery($request->driver);
            $queryOne .= " AND it.driver_id_one IN ($params) ";
            $queryTwo .= " AND it.driver_id_two IN ($params) ";            
        }

        if( !empty($havingConditions) ){
            $queryHaving = " HAVING " . implode(' AND ', $havingConditions);
        }

        $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        $items = collect($items)
        ->values() // asegurar índices consecutivos
        ->map(function ($item, $index) {

            $operation_status = auth()->user()->getOperationStatus($item);
            $service_status   = auth()->user()->getServiceStatusOP($item);

            $background_color = '#D9D9D9'; // gris por defecto
            $priority = 1;

            // VERDE
            if (in_array($operation_status, ['OK', 'CONFIRMADO']) && $service_status === 'COMPLETADO') {
                $background_color = '#92D050';
                $priority = 3;
            }

            // ROJO
            if ($operation_status === 'NO SE PRESENTÓ' && $service_status === 'NO SE PRESENTÓ') {
                $background_color = '#F79999';
                $priority = 2;
            }

            // Agregamos campos dinámicamente
            $item->row_background_color = $background_color;
            $item->row_priority = $priority;
            $item->original_index = $index; // para mantener orden estable

            return $item;
        })
        ->sortBy([
            ['row_priority', 'asc'],     // gris (1), rojo (2), verde (3)
            ['original_index', 'asc'],   // mantener orden original
        ])
        ->values()->toArray(); // reindexar

        return view('management.operations.index', [
            'items' => $items,
            'date' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ),
            'nexDate' => date('Y-m-d', strtotime($request->date . ' +1 day')),
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestión de operaciones del: ".$date,
                    "active" => true
                ]
            ],
            'types_cancellations' => ApiTrait::makeTypesCancellations(),
            'data' => $data,
            'request' => $request->input(),
        ]);
    }

    public function preassignments(Request $request){
        try {
            //DECLARAMOS VARIABLES
            $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one   AND :init_date_two  AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND ( it.op_one_status != 'CANCELLED' OR  (it.op_one_status = 'CANCELLED' AND (it.op_one_cancellation_level = 'OPERATION' OR it.op_one_cancellation_level IS NULL)) ) ";
            $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND ( it.op_two_status != 'CANCELLED' OR  (it.op_two_status = 'CANCELLED' AND (it.op_two_cancellation_level = 'OPERATION' OR it.op_two_cancellation_level IS NULL)) ) AND it.is_round_trip = 1 ";
            $havingConditions = []; $queryHaving = "";
            $queryData = [
                'init' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 00:00:00",
                'end' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 23:59:59",
            ];            
    
            //CONSULTAMOS SERVICIOS
            $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);
    
            //RECORREMOS LOS SERVICIOS PARA PODER REALISAR LA PREASIGNACION
            if( sizeof($items)>=1 ):

                $next_available_preassignment = $this->extractNextAvailablePreassignments( isset( $request->date ) ? $request->date : date("Y-m-d") );

                $arrival_current_counter = $next_available_preassignment['next_L'];
                $transfer_current_counter = $next_available_preassignment['next_T'];
                $departure_current_counter = $next_available_preassignment['next_S'];

                foreach($items as $key => $item):
                    $preassignment = "";
                    $service = ReservationsItem::find($item->id); //ES LA INFORMACION DEL SERVICIO
                    if( !$service->op_one_preassignment && $item->final_service_type == 'ARRIVAL' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $preassignment = "L".$arrival_current_counter;
                        $service->op_one_preassignment = $preassignment;
                        $arrival_current_counter ++;
                    }
                    if( !$service->op_two_preassignment && $item->final_service_type == 'ARRIVAL' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $preassignment = "L".$arrival_current_counter;
                        $service->op_two_preassignment = $preassignment;
                        $arrival_current_counter ++;
                    }                                     

                    if( !$service->op_one_preassignment && $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $preassignment = "T".$transfer_current_counter;
                        $service->op_one_preassignment = $preassignment;
                        $transfer_current_counter ++;
                    }
                    if( !$service->op_two_preassignment && $item->final_service_type == 'TRANSFER' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $preassignment = "T".$transfer_current_counter;
                        $service->op_two_preassignment = $preassignment;
                        $transfer_current_counter ++;
                    }

                    if( !$service->op_one_preassignment && $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ){
                        $preassignment = "S".$departure_current_counter;
                        $service->op_one_preassignment = $preassignment;
                        $departure_current_counter ++;
                    }
                    if( !$service->op_two_preassignment && $item->final_service_type == 'DEPARTURE' && $item->op_type == "TYPE_TWO" && ( $item->is_round_trip == 1 ) ){
                        $preassignment = "S".$departure_current_counter;
                        $service->op_two_preassignment = $preassignment;
                        $departure_current_counter ++;
                    }
                    if($preassignment !== "") {
                        $service->save();
    
                        //CREAMOS UN LOG
                        $this->create_followUps($item->reservation_id, "Se pre-asigno de (NULL) por ".$preassignment. " al servicio: ".$item->id.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);
                    }
                endforeach;
            endif;

            return response()->json([
                'success' => true,
                'message' => 'Se pre-asignaron los servicios de manera correcta...',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function closeOperation(Request $request){
        try {
            ini_set('memory_limit', '-1'); // Sin límite
            set_time_limit(120); // Aumenta el límite a 60 segundos

            if( !auth()->user()->hasPermission(85) ){
                return response()->json([
                    'errors' => [
                        'code' => 'NOT_PERMISSIONS',
                        'message' => 'No cuenta con permisos para realizar esta acción.'
                    ],
                    'status' => 'error',
                    'success' => false,
                    'message' => 'No cuenta con permisos para realizar esta acción'
                ], 403);                
            }

            //DECLARAMOS VARIABLES
            $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one   AND :init_date_two  AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.op_one_operation_close = 0 AND ( it.op_one_status != 'CANCELLED' OR  (it.op_one_status = 'CANCELLED' AND (it.op_one_cancellation_level = 'OPERATION' OR it.op_one_cancellation_level IS NULL)) ) ";
            $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.op_two_operation_close = 0 AND ( it.op_two_status != 'CANCELLED' OR  (it.op_two_status = 'CANCELLED' AND (it.op_two_cancellation_level = 'OPERATION' OR it.op_two_cancellation_level IS NULL)) ) AND it.is_round_trip = 1 ";
            $havingConditions = []; $queryHaving = "";
            $queryData = [
                'init' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 00:00:00",
                'end' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 23:59:59",
            ];
    
            //CONSULTAMOS SERVICIOS
            $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);
            if(empty($items)){
                return response()->json([
                    'status'    => 'info',
                    'success'   => true,
                    'message'   => 'La operación ya se encuentra cerrada correctamente',
                ], 200);
            }

            $errors = $this->validateServiceQualification($items);
            if(!empty($errors)){
                return response()->json([
                    'status'    => 'error',
                    'success'   => false,
                    'message'   => 'La operación no pudo cerrarse ya que hay servicios que no están calificados correctamente.',
                    'items'      => $errors
                ], 200);
            }

            //RECORREMOS LOS SERVICIOS PARA PODER REALISAR LA PREASIGNACION
            if( sizeof($items)>=1 ):
                foreach($items as $key => $item):
                    $service = ReservationsItem::find($item->id);
                    
                    if( $item->op_type == "TYPE_ONE" ){
                        $service->op_one_operation_close = 1;
                    }

                    if( $item->op_type == "TYPE_TWO" ){
                        $service->op_two_operation_close = 1;
                    }                    

                    $service->save();
                endforeach;
            endif;

            return response()->json([
                'status'    => 'success',
                'success'   => true,
                'message'   => 'Se cerro la operación correctamente',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function validateServiceQualification($items): array
    {
        $errors = [];

        foreach($items as $key => $item):
            if( $item->op_type == "TYPE_ONE" && ( $item->one_service_status == "PENDING" || ( $item->one_service_status == "COMPLETED" && $item->one_service_operation_status != "OK" ) ) ){
                array_push($errors, $item->code);
            }

            if( $item->op_type == "TYPE_TWO" && ( $item->two_service_status == "PENDING" || ( $item->two_service_status == "COMPLETED" && $item->two_service_operation_status != "OK" ) ) ){
                array_push($errors, $item->code);
            }
        endforeach;

        return $errors;
    }

    public function openOperation(Request $request){
        try {

            if( !auth()->user()->hasPermission(85) ){
                return response()->json([
                    'errors' => [
                        'code' => 'NOT_PERMISSIONS',
                        'message' => 'No cuenta con permisos para realizar esta acción.'
                    ],
                    'status' => 'error',
                    'success' => false,
                    'message' => 'No cuenta con permisos para realizar esta acción'
                ], 403);                
            }

            //DECLARAMOS VARIABLES
            $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one   AND :init_date_two  AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.op_one_operation_close = 1 AND ( it.op_one_status != 'CANCELLED' OR  (it.op_one_status = 'CANCELLED' AND (it.op_one_cancellation_level = 'OPERATION' OR it.op_one_cancellation_level IS NULL)) ) ";
            $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.op_two_operation_close = 1 AND ( it.op_two_status != 'CANCELLED' OR  (it.op_two_status = 'CANCELLED' AND (it.op_two_cancellation_level = 'OPERATION' OR it.op_two_cancellation_level IS NULL)) ) AND it.is_round_trip = 1 ";
            $havingConditions = []; $queryHaving = "";
            $queryData = [
                'init' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 00:00:00",
                'end' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 23:59:59",
            ];
    
            //CONSULTAMOS SERVICIOS
            $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);
            if(empty($items)){
                return response()->json([
                    'status'    => 'info',
                    'success'   => true,
                    'message'   => 'No hay operación que abrir',
                ], 200);                
            }            
    
            //RECORREMOS LOS SERVICIOS PARA PODER REALISAR LA PREASIGNACION
            if( sizeof($items)>=1 ):
                foreach($items as $key => $item):
                    $service = ReservationsItem::find($item->id);

                    if( $item->op_type == "TYPE_ONE" ){
                        $service->op_one_operation_close = 0;
                    }

                    if( $item->op_type == "TYPE_TWO" ){
                        $service->op_two_operation_close = 0;
                    }

                    $service->save();
                endforeach;
            endif;

            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Se abrio la operación correctamente',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function preassignment(Request $request){
        try {
            DB::beginTransaction();

            $next_available_preassignment = $this->extractNextAvailablePreassignments( isset( $request->date ) ? $request->date : date("Y-m-d") );

            $arrival_current_counter = $next_available_preassignment['next_L'];
            $transfer_current_counter = $next_available_preassignment['next_T'];
            $departure_current_counter = $next_available_preassignment['next_S'];
                        
            $preassignment = "";
            //OBTENEMOS INFORMACION DEL SERVICIO
            $service = ReservationsItem::find($request->reservation_item);
            // dd($request, $service);
            //REALIZAMOS LA PREASIGNACION DEPENDIENDO DE LA OPERACION
            if( $request->operation == 'ARRIVAL' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $preassignment = "L$arrival_current_counter";
                $service->op_one_preassignment = $preassignment;
                $arrival_current_counter++;
            }
            if( $request->operation == 'ARRIVAL' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $preassignment = "L$arrival_current_counter";
                $service->op_two_preassignment = $preassignment;
            }

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $preassignment = "T$transfer_current_counter";
                $service->op_one_preassignment = $preassignment;
                $transfer_current_counter++;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $preassignment = "T$transfer_current_counter";
                $service->op_two_preassignment = $preassignment;
            }

            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $preassignment = "S$departure_current_counter";
                $service->op_one_preassignment = $preassignment;
                $departure_current_counter ++;
            }            
            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $preassignment = "S$departure_current_counter";
                $service->op_two_preassignment = $preassignment;
            }               
            $service->save();

            //CREAMOS UN LOG
            $this->create_followUps($service->reservation_id, "Se pre-asigno de (NULL) por ".$preassignment. " al servicio: ".$service->id.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se pre-asigno el servicio de manera correcta...',
                'data' => array(
                    "item"  => $request->id,
                    "operation"  => $request->operation,
                    "value"  => $preassignment,
                    "message" => "Se pre-asigno de (NULL) por ".$preassignment. " al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // VAMOS A VALIDAR PRIMERO SI LAS ZONAS DEL SERVICIO CUENTA CON COSTO OPERATIVO
    public function validateOperatingCosts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',            
            'item_id' => 'required|string',
            'service_id' => 'required|integer',
            // 'service' => 'required|string|in:ARRIVAL,DEPARTURE,TRANSFER',
            // 'type' => 'required|string|in:TYPE_ONE,TYPE_TWO',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                'success' => false,
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        // Obtener el item de la reservación
        $item = ReservationsItem::with('reservations.site')->where('id', $request->item_id)->first();
        
        if (!$item) {
            return response()->json([
                'errors' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Item no encontrado'
                ],
                'status' => 'error',
                'success' => false,
                'message' => 'Ítem no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        // dd($item->toArray());
        try {
            $codeSite = $item->reservations->site->id;
            $siteIsCxC = $item->reservations->site->is_cxc;
            $siteType = $item->reservations->site->type_site;
            $rateGroup = RatesGroup::where('code', $item->reservations->rate_group)->first();

            $params['destination_id'] = 1;
            $params['zone_one'] = $item->from_zone;
            $params['zone_two'] = $item->to_zone;
            $params['zone_three'] = $item->to_zone;
            $params['zone_four'] = $item->from_zone;
            if( $siteType != "AGENCY" ){
                $table = 'rates_transfers';
                $leftJoin = 'LEFT JOIN rates_groups as rg ON rg.id = rt.rate_group_id';
                $query = 'AND rg.id = :rate_group';
                $params['rate_group'] = $rateGroup->id;
            }else{
                $table = 'rates_enterprises';
                $leftJoin = 'LEFT JOIN enterprises as e ON e.id = rt.enterprise_id';
                $query = 'AND e.id = :enterprise_id';
                $params['enterprise_id'] = $item->reservations->site->enterprise_id;
            }
            $params['destination_service_id'] = $request->service_id;

            $rates = DB::select("SELECT 
                                        ds.name as service_name, ds.price_type,
                                        rt.*,
                                        zoneOne.name as from_name,
                                        zoneTwo.name as to_name
                                    FROM {$table} as rt
                                        LEFT JOIN destination_services as ds ON ds.id = rt.destination_service_id
                                        {$leftJoin}
                                        LEFT JOIN zones as zoneOne ON zoneOne.id = rt.zone_one
                                        LEFT JOIN zones as zoneTwo ON zoneTwo.id = rt.zone_two
                                    WHERE rt.destination_id = :destination_id
                                    AND ( (rt.zone_one = :zone_one AND rt.zone_two = :zone_two) OR ( rt.zone_one = :zone_three AND rt.zone_two = :zone_four )  )                                     
                                    AND rt.destination_service_id = :destination_service_id
                                    {$query}", $params);

            // dd($rates, $rates[0]->operating_cost);
            if( $rates ){
                return response()->json([
                    'status' => 'success',
                    'success' => true,
                    'value' => $rates[0]->operating_cost,
                    'codeRate' => $rates[0]->id,
                    'siteType' => $siteType,
                    'message' => 'Se valido correctamente el costo operativo',
                ], Response::HTTP_OK);
            }else{
                $rate = ( $siteType != "AGENCY" ? new RatesTransfer() : new RatesEnterprise() );
                if( $siteType != "AGENCY" ){
                    $rate->rate_group_id = $rateGroup->id;
                }else{
                    $rate->enterprise_id = $item->reservations->site->enterprise_id;
                }                
                $rate->destination_service_id = $request->service_id;
                $rate->destination_id = 1;
                $rate->zone_one = $item->from_zone;
                $rate->zone_two = $item->to_zone;

                $rate->one_way = isset($request->one_way) ? $request->one_way : '0.00';
                $rate->round_trip = isset($request->round_trip) ? $request->round_trip : '0.00';
                $rate->ow_12 = isset($request->ow_12) ? $request->ow_12 : '0.00';
                $rate->rt_12 = isset($request->rt_12) ? $request->rt_12 : '0.00';
                $rate->ow_37 = isset($request->ow_37) ? $request->ow_37 : '0.00';
                $rate->rt_37 = isset($request->rt_37) ? $request->rt_37 : '0.00';
                $rate->up_8_ow = isset($request->up_8_ow) ? $request->up_8_ow : '0.00';
                $rate->up_8_rt = isset($request->up_8_rt) ? $request->up_8_rt : '0.00';
                $rate->operating_cost = isset($request->operating_cost) ? $request->operating_cost : '0.00';
                $rate->save();
                return response()->json([
                    'status' => 'success',
                    'success' => true,
                    'value' => NULL,
                    'codeRate' => $rate->id,
                    'siteType' => $siteType,
                    'message' => 'Se valido correctamente el costo operativo',
                ], Response::HTTP_OK);                
            }
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],                
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);            
        }
    }

    //SETEMOS LA UNIDAD E INGRESAMOS EL MONTO OPERATIVO, DEL SERVICIO
    public function setVehicle(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',            
            'item_id' => 'required|string',
            'service' => 'required|string|in:ARRIVAL,DEPARTURE,TRANSFER',
            'type' => 'required|string|in:TYPE_ONE,TYPE_TWO',
            'vehicle_id' => 'required|integer',
            'operating_cost' => 'required',
            'code_rate' => 'required',
            'site_type' => 'required|string',
            'date' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                'success' => false,
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        // Obtener el item de la reservación
        $item = ReservationsItem::with('reservations.site')->where('id', $request->item_id)->first();
        
        if (!$item) {
            return response()->json([
                'errors' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Item no encontrado'
                ],
                'status' => 'error',
                'success' => false,
                'message' => 'Ítem no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        if( !auth()->user()->hasPermission(86) ){
            return response()->json([
                'errors' => [
                    'code' => 'NOT_PERMISSIONS',
                    'message' => 'No cuenta con permisos para realizar esta acción.'
                ],
                'status' => 'error',
                'success' => false,
                'message' => 'No cuenta con permisos para realizar esta acción'
            ], 403);                
        }

        try {
            DB::beginTransaction();
            //OBTENEMOS INFORMACION
            $infoUnitDriver = DriverSchedule::where('date', $request->date)
                                            ->where('vehicle_id', $request->vehicle_id)
                                            ->whereNull('end_check_out_time')
                                            ->whereNull('deleted_at')
                                            ->where('is_open', 1)
                                            ->first();

            $driver = Driver::find(( isset($infoUnitDriver->driver_id) ? $infoUnitDriver->driver_id : 0 ));
            // dd($infoUnitDriver->toArray(), $driver->toArray());
            
            $vehicle_new = Vehicle::find($request->vehicle_id);

            if( !$vehicle_new ) {
                if($request->type == "TYPE_ONE"):
                    $item->vehicle_id_one = null;
                    $item->op_one_operating_cost = 0;
                endif;

                if($request->type == "TYPE_TWO"):
                    $item->vehicle_id_two = null;
                    $item->op_two_operating_cost = 0;
                endif;

                if( $driver ){
                    if($request->type == "TYPE_ONE"):
                        $item->driver_id_one = null;
                    endif;
        
                    if($request->type == "TYPE_TWO"):
                        $item->driver_id_two = null;
                    endif;
                }
                
                $item->save();

                $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", desasignó la unidad.", 'HISTORY', "UPDATE_SERVICE_VEHICLE");

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'success' => true,
                    'message' => 'Se removió la asignación correctamente a la unidad',
                    'data' => array(
                        "item"  => $request->id,
                        "value"  => 0,
                        "name" => "Sin asignar",
                        "cost"  => 0,
                        "message" => "Actualización de unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" )."). Se removió la asignación al servicio: ".$item->id.", por ".auth()->user()->name
                    ),
                    'data2' => array(
                        "item"  => $request->id,
                        "value"  => ( isset($driver->id) ? $driver->id : NULL ),
                        "name" => ( isset($driver->id) ? $driver->names.' '.$driver->surnames : NULL ),
                        "message" => "Se desasignó el conductor, por ".auth()->user()->name
                    )                
                ], Response::HTTP_OK);
            }

            $vehicleIdOld = ( $request->type == 'TYPE_ONE' ? $item->vehicle_id_one : $item->vehicle_id_two );
            $operatingCostOld = ( $request->type == 'TYPE_ONE' ? $item->op_one_operating_cost : $item->op_two_operating_cost );
            $vehicle_current = Vehicle::find(( $request->type == 'TYPE_ONE' ? $item->vehicle_id_one : $item->vehicle_id_two ));

            if($request->type == "TYPE_ONE"):
                $item->vehicle_id_one = $request->vehicle_id;
                $item->op_one_operating_cost = $request->operating_cost;
            endif;

            if($request->type == "TYPE_TWO"):
                $item->vehicle_id_two = $request->vehicle_id;
                $item->op_two_operating_cost = $request->operating_cost;
            endif;

            if( $driver ){
                if($request->type == "TYPE_ONE"):
                    $item->driver_id_one = $driver->id;
                endif;
    
                if($request->type == "TYPE_TWO"):
                    $item->driver_id_two = $driver->id;
                endif;
            }

            $item->save();

            $rate = ( $request->site_type != "AGENCY" ? RatesTransfer::find($request->code_rate) : RatesEnterprise::find($request->code_rate) );
            if( $rate->operating_cost == NULL || $rate->operating_cost == 0 ){
                $rate->operating_cost = $request->operating_cost;
                $rate->save();
            }

            //CREAMOS UN LOG
            $text_vehicle = ( $vehicleIdOld == NULL ? "asigno la unidad: (".$vehicle_new->name.")" : "actualizo la unidad de: (".$vehicle_current->name.") a (".$vehicle_new->name.")" );
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", ".$text_vehicle.", de la (".$request->service."), con ID: ".$item->id, 'HISTORY', "UPDATE_SERVICE_VEHICLE");
            // $this->create_followUps($service->reservation_id, "Actualización de unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" ).") por ".$vehicle_new->name. " al servicio: ".$service->id.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            $text_operating_cost = ( $operatingCostOld == NULL ? "asigno el costo operativo: (".$request->operating_cost.")" : "actualizo el costo operativo de: (".$operatingCostOld.") a (".$request->operating_cost.")" );
            $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", ".$text_operating_cost.", de la (".$request->service."), con ID: ".$item->id, 'HISTORY', "UPDATE_SERVICE_OPERATING_COST");
            // $this->create_followUps($service->reservation_id, "Actualización de costo operativo por ".$request->value. " al servicio: ".$service->id.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            if( $driver ){
                $text_driver = "asigno al conductor: (".$driver->names.' '.$driver->surnames.")";
                $this->create_followUps($item->reservation_id, "El usuario: ".auth()->user()->name.", ".$text_driver.", de la (".$request->service."), con ID: ".$item->id, 'HISTORY', "UPDATE_SERVICE_DRIVER");
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'success' => true,
                'message' => 'Se asigno correctamente la unidad',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->vehicle_id,
                    "name" => $vehicle_new->name,
                    "cost"  => $request->operating_cost,
                    "message" => "Actualización de unidad (".( isset($vehicle_current->name) ? $vehicle_current->name : "NULL" ).") por ".$vehicle_new->name. " y costo de operación ".$request->operating_cost." al servicio: ".$item->id.", por ".auth()->user()->name
                ),
                'data2' => array(
                    "item"  => $request->id,
                    "value"  => ( isset($driver->id) ? $driver->id : NULL ),
                    "name" => ( isset($driver->id) ? $driver->names.' '.$driver->surnames : NULL ),
                    "message" => "Se asigno al conductor (".( isset($driver->id) ? $driver->names.' '.$driver->surnames : NULL )."), por ".auth()->user()->name
                )                
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],                
                'status' => 'error',
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);              
        }
    }

    public function setDriver(Request $request)
    {
        if( !auth()->user()->hasPermission(87) ){
            return response()->json([
                'errors' => [
                    'code' => 'NOT_PERMISSIONS',
                    'message' => 'No cuenta con permisos para realizar esta acción.'
                ],
                'status' => 'error',
                'success' => false,
                'message' => 'No cuenta con permisos para realizar esta acción'
            ], 403);                
        }

        try {
            DB::beginTransaction();
            //OBTENEMOS INFORMACION
            $service = ReservationsItem::find($request->reservation_item);        
            $driver_new = Driver::find($request->driver_id);

            //ACTUALIZAMOS INFORMACION
            if( $request->operation == 'ARRIVAL' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_one);
                $service->driver_id_one = $request->driver_id;
            }
            if( $request->operation == 'ARRIVAL' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_two);
                $service->driver_id_two = $request->driver_id;
            }

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_one);
                $service->driver_id_one = $request->driver_id;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_two);
                $service->driver_id_two = $request->driver_id;
            }

            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_one);
                $service->driver_id_one = $request->driver_id;
            }            
            if( $request->operation == 'DEPARTURE' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $driver_current = Driver::find($service->driver_id_two);
                $service->driver_id_two = $request->driver_id;
            }            
            $service->save();            

            //CREAMOS UN LOG
            $this->create_followUps($service->reservation_id, "Se asigno al conductor (".( isset($driver_current->names) ? $driver_current->names." ".$driver_current->surnames : "NULL" ).") por ".$driver_new->names." ".$driver_new->surnames. " al servicio: ".$service->id.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se asigno correctamente el conductor',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->driver_id,
                    "name" => $driver_new->names." ".$driver_new->surnames,
                    "message" => "Se asigno al conductor (".( isset($driver_current->names) ? $driver_current->names." ".$driver_current->surnames : "NULL" ).") por ".$driver_new->names." ".$driver_new->surnames. " al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //ACTUALIZAMOS EL ESTATUS E INGRESAMOS LA HORA OPERATIVA, DEL SERVICIO
    public function updateStatusOperation(Request $request){
        try {
            DB::beginTransaction();            
            $service = ReservationsItem::find($request->item_id);

            if( $request->operation == "ARRIVAL" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_status_operation = $request->status;
                ( isset($request->time) ? $service->op_one_time_operation = $request->time : "" );
            endif;
            if( $request->operation == "ARRIVAL" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_status_operation = $request->status;
                ( isset($request->time) ? $service->op_two_time_operation = $request->time : "" );
            endif;

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $service->op_one_status_operation = $request->status;
                ( isset($request->time) ? $service->op_one_time_operation = $request->time : "" );
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $service->op_two_status_operation = $request->status;
                ( isset($request->time) ? $service->op_two_time_operation = $request->time : "" );
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_status_operation = $request->status;
                ( isset($request->time) ? $service->op_one_time_operation = $request->time : "" );
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_status_operation = $request->status;
                ( isset($request->time) ? $service->op_two_time_operation = $request->time : "" );
            endif;
            $service->save();
            
            $this->create_followUps($service->reservation_id, "Actualización de estatus de operación (".$request->operation.") por ".$request->status.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            $this->create_followUps($service->reservation_id, "Actualización de hora operación (".$request->operation.") por ".$request->time.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Estatus de operación, actualizado con éxito',
                'data' => array(
                    "item"  => $request->id,
                    "value"  => $request->status,
                    "time"  => ( $request->time != "" ? date("H:i", strtotime($request->time)) : NULL ),
                    "message" => "Actualización de estatus y horario de operación (".$request->type.") por ".$request->status." y ".$request->time." al servicio: ".$service->id.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function createService(Request $request){
        try {
            DB::beginTransaction();

            $errors = [
                'reference' => 'nullable|string|max:255',
                'site_id' => 'required|integer|exists:sites,id',
                'language' => 'required|in:en,es',

                'client_first_name' => 'required|string|max:255',
                'client_last_name' => 'required|string|max:255',

                'from_zone_id' => 'required|integer|exists:zones,id',
                'from_name' => 'required|string|max:255',
                'to_zone_id' => 'required|integer|exists:zones,id',
                'to_name' => 'required|string|max:255',

                'passengers' => 'required|integer|max:255',
                'departure_date' => 'required|date_format:Y-m-d H:i',
                'destination_service_id' => 'required|integer|exists:destination_services,id',
                // 'comments' => 'string|max:255',

                'sold_in_currency' => 'required|in:MXN,USD',
                'total' => 'required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            ];

            if( isset($request->is_open) && $request->is_open == 1 ){
                $errors['open_service_time'] = 'required|numeric';
            }

            $validator = Validator::make($request->all(), $errors);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => [
                        'code' => 'required_params',
                        'message' =>  $validator->errors()->all() 
                    ],
                    'message' => $validator->errors()->all()
                ], Response::HTTP_BAD_REQUEST);
            }

            //VALIDAMOS SI LA REFERENCIA YA EXISTE
            // if( $request->type_service == "PRIVATE" ){
            if( $request->reference ){
                $duplicated_reservation = Reservation::where('reference', $request->reference)->count();
                if( $duplicated_reservation ) {
                    return response()->json([
                        'errors' => [
                            'code' => 'required_params',
                        ],
                        'message' => 'Ese folio ya ha sido registrado',
                    ], Response::HTTP_BAD_REQUEST); 
                }
            }
            // }

            // if( $request->type_service == "SHARED" ){
            //     $duplicated_reservation = Reservation::where('reference', $request->reference)->first();
            //     if( $duplicated_reservation != NULL ){
            //         $reservation = $duplicated_reservation;
            //     }
            // }

            //FORMATEAMOS LA FECHA DEL SERVICIO PARA PODER VER SI ACTUALIZAREMOS LA TABLA
            // Crear una instancia de DateTime a partir de la cadena de fecha y hora
            $dateTime = new DateTime($request->departure_date);            
            // Obtener solo la fecha en el formato deseado
            $departure_date = $dateTime->format('Y-m-d');
            $departure_date_today = ( $departure_date == date('Y-m-d') ? true : false );

            $default_destination_id = 1; // Considerando que el id corresponde a: "Cancún"
            $destination = Destination::find( $default_destination_id );
    
            $from_coordinates = $this->getLatLngByZoneId( $request->from_zone_id );
            $to_coordinates = $this->getLatLngByZoneId( $request->to_zone_id );
    
            $from_lat = $from_coordinates['lat'];
            $from_lng = $from_coordinates['lng'];
            $to_lat = $to_coordinates['lat'];
            $to_lng = $to_coordinates['lng'];
    
            $from_zone = Zones::find( $request->from_zone_id );
            $to_zone = Zones::find( $request->to_zone_id );
    
            $destination_service = DestinationService::find( $request->destination_service_id );
            if( !$destination_service ){
                return response()->json([
                    'errors' => [
                        'code' => 'required_params',
                    ],                
                    'message' => 'No se encontró el vehículo',
                ], Response::HTTP_BAD_REQUEST);
            }

            // Creando reservación
            //if( ($request->type_service == "PRIVATE") || ($request->type_service == "SHARED" && $duplicated_reservation == NULL) ){
                $reservation = new Reservation;
                $reservation->client_first_name = $request->client_first_name;
                $reservation->client_last_name = $request->client_last_name;
                $reservation->client_email = $request->client_email ? $request->client_email : null;
                $reservation->client_phone = $request->client_phone ? $request->client_phone : null;
                $reservation->currency = $request->sold_in_currency;
                $reservation->language = $request->language;
                $reservation->rate_group = '0B842B8C';
                $reservation->is_commissionable = 0;
                $reservation->pay_at_arrival = 1;
                $reservation->site_id = $request->site_id;
                $reservation->destination_id = $destination ? $default_destination_id : null;
                $reservation->user_id = auth()->user()->id;
                $reservation->reference = $request->reference;
                $reservation->created_at = Carbon::now();
                $reservation->updated_at = Carbon::now();
                $reservation->comments = $request->comments;
                $reservation->is_complete = ( $request->site_id == 11 || $request->site_id == 21 ? 0 : 1 );
                $reservation->is_last_minute = 1;
                $reservation->origin_sale_id = $request->origin_sale_id;
                $reservation->save();

                

                // Creando follow_up
                $this->create_followUps($reservation->id, 'SE CAPTURÓ LA VENTA CON ID: '.$reservation->id.', POR EL USUARIO: '.auth()->user()->name.', DESDE EL PANEL DE OPERACIONES', 'HISTORY', auth()->user()->name);
            //}

            $item = new ReservationsItem();
            $item->reservation_id = $reservation->id;
            $item->code = $this->generateCode();
            // if( $request->type_service == "SHARED" ){
            //     $item->client_first_name = $request->client_first_name;
            //     $item->client_last_name = $request->client_last_name;
            // }
            $item->destination_service_id = $request->destination_service_id;
            $item->from_name = $request->from_name ? $request->from_name : $from_zone->name;
            $item->from_lat = $from_lat;
            $item->from_lng = $from_lng;
            $item->from_zone = $request->from_zone_id;
            $item->to_name = $request->to_name ? $request->to_name : $to_zone->name;
            $item->to_lat = $to_lat;
            $item->to_lng = $to_lng;
            $item->to_zone = $request->to_zone_id;
            $item->distance_time = $to_zone->time ? $this->timeToSeconds( $to_zone->time ) : 0;
            $item->distance_km = $to_zone->distance ? $to_zone->distance : '';
            $item->is_round_trip = 0;
            $item->passengers = $request->passengers;
            $item->op_one_status = 'PENDING';
            $item->op_one_pickup = $request->departure_date;
            $item->op_two_status = 'PENDING';
            if( isset($request->is_open) && $request->is_open == 1 ){
                $item->is_open = $request->is_open;
                $item->open_service_time = $request->open_service_time;
            }            
            $item->created_at = Carbon::now();
            $item->updated_at = Carbon::now();
            $item->save();

            $this->create_followUps($reservation->id, 'SE CREO EL SERVICIO: '.$item->code.' PARA LA VENTA CON ID: '.$reservation->id.', POR EL USUARIO: '.auth()->user()->name.', DESDE EL PANEL DE OPERACIONES', 'HISTORY', auth()->user()->name);

            // Creando Sale
            $sale = new Sale();
            $sale->reservation_id = $reservation->id;
            $sale->description = $destination_service->name . ' | ' . 'One Way';
            $sale->quantity = 1;
            $sale->total = $request->total;
            $sale->created_at = Carbon::now();
            $sale->updated_at = Carbon::now();
            $sale->save();

            $this->create_followUps($reservation->id, 'SE CAPTURO EL MONTO: '.$request->total.' PARA LA VENTA CON ID: '.$reservation->id.', POR EL USUARIO: '.auth()->user()->name.', DESDE EL PANEL DE OPERACIONES', 'HISTORY', auth()->user()->name);

            DB::commit();

            return response()->json([
                'success' => true,
                'date' => $departure_date,
                'today' => $departure_date_today,
                'message' => 'Se agrego servicio correctamente, para '.(  $departure_date_today ? " el día de hoy, es necesario recargar la pagina para actualizar la información " : " la fecha ".$departure_date ),
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function addComment(Request $request){
        try {
            DB::beginTransaction();
            $service = ReservationsItem::find($request->code);
            $action = ( ( ( $request->type == "ARRIVAL" ) && $service->op_one_comments == "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) && $service->op_one_comments == "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) && $service->op_two_comments == "" ) ? "agrego" : ( ( ( $request->type == "ARRIVAL" ) && $service->op_one_comments != "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) && $service->op_one_comments != "" ) || ( ( $request->operation == 'TRANSFER' || $request->type == "DEPARTURE" ) && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) && $service->op_two_comments != "" ) ? "actualizo" : "" ) );

            if( $request->operation == "ARRIVAL" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_comments = $request->comment;
            endif;
            if( $request->operation == "ARRIVAL" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_comments = $request->comment;
            endif;            

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $service->op_one_comments = $request->comment;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $service->op_two_comments = $request->comment;
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $service->op_one_comments = $request->comment;
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $service->op_two_comments = $request->comment;
            endif;
            $service->save();
            
            $this->create_followUps($service->reservation_id, "Se agrego un comentario al servicio: ".$request->code.", por ".auth()->user()->name, 'HISTORY', auth()->user()->name);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Se '.$action.' comentario exitosamente...',
                'data' => array(
                    "item"  => $request->id, //ITEM DE LA TABLA DE OPERACIONES
                    "value"  => $request->comment, // EL MENSAJE QUE SE COLOGO
                    "reservation" => $service->reservation_id, // EL ID DE LA RESERVACIÓN
                    "status"  => 1,
                    "message" => "Se agrego un comentario al servicio: ".$request->code.", por ".auth()->user()->name
                )
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getComment(Request $request)
    {
        try {
            //DECLARACION DE VARIABLES
            $message = "";
            $service = ReservationsItem::find($request->item_id);

            if( $request->operation == "ARRIVAL" ):
                $message = $service->op_one_comments;
            endif;

            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ){
                $message = $service->op_one_comments;
            }
            if( $request->operation == 'TRANSFER' && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ){
                $message = $service->op_two_comments;
            }

            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_ONE' && ( $service->is_round_trip == 0 || $service->is_round_trip == 1 ) ):
                $message = $service->op_one_comments;
            endif;
            if( $request->operation == "DEPARTURE" && $request->type == 'TYPE_TWO' && ( $service->is_round_trip == 1 ) ):
                $message = $service->op_two_comments;
            endif;            

            return response()->json([
                'success' => true,
                'message' => $message,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getHistory(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [            
                'code' => 'required',            
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'errors' => [
                        'code' => 'REQUIRED_PARAMS',
                        'message' =>  $validator->errors()->all()
                    ],
                    "message" => $validator->errors()->all(),
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }
    
            $xHTML  = '';
            $reservation = $this->getReservation($request->code);

            if (!$reservation) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reservación no encontrada'
                ], Response::HTTP_NOT_FOUND);
            }

            $xHTML .= '[<strong>(DESDE):</strong> '. $reservation->items[0]->from_name.' [<strong>LATITUD:</strong> '.$reservation->items[0]->from_lat.'] - [<strong>LONGITUD:</strong> '.$reservation->items[0]->from_lng.'] ] </br> </br>';
            $xHTML .= '[<strong>(HACIA):</strong> '. $reservation->items[0]->to_name.'   [<strong>LATITUD:</strong> '.$reservation->items[0]->to_lat.']   - [<strong>LONGITUD:</strong> '.$reservation->items[0]->to_lng.'] ] </br> </br>';
            foreach ($reservation->followUps as $followUp) {
                $xHTML .= '[('.$followUp->type.') '. $followUp->text.'] </br> </br>';
            }

            return response()->json([
                'success' => ( !empty($xHTML) ? true : false ),
                'message' => $xHTML,
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);       
        }

        // try {
        //     //DECLARACION DE VARIABLES
        //     $message = $this->getMessages($request->code);

        //     return response()->json([
        //         'success' => ( !empty($message) ? true : false ),
        //         'message' => $message,
        //     ], 200);
        // } catch (Exception $e) {
        //     return response()->json([
        //         'errors' => [
        //             'code' => 'internal_server',
        //             'message' => $e->getMessage()
        //         ],
        //         'message' => $e->getMessage()
        //     ], 500);
        // }
    }

    public function getDataCustomer(Request $request)
    {
        try {
            //DECLARACION DE VARIABLES
            $booking = Reservation::where('id',$request->code)->first();

            return response()->json([
                'success' => ( !empty($booking) ? true : false ),
                'data' => $booking,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'internal_server',
                    'message' => $e->getMessage()
                ],
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function timeToSeconds($time)
    {
        $parts = explode(' ', $time);
        
        $hours = 0;
        $minutes = 0;
        
        foreach ($parts as $key => $part) {
            if ($part == 'H') {
                $hours = (int)$parts[$key - 1];
            } elseif ($part == 'Min') {
                $minutes = (int)$parts[$key - 1];
            }
        }
        
        $seconds = $hours * 3600 + $minutes * 60;
        
        return $seconds;
    }

    private function getLatLngByZoneId($zone_id)
    {
        $equivalences = [
            1 => [
                'lat' => 21.0442754,
                'lng' => -86.8772972,
            ],
            2 => [
                'lat' => 21.135166,
                'lng' => -86.746224,
            ],
            3 => [
                'lat' => 21.1831607,
                'lng' => -86.8087541,
            ],
            4 => [
                'lat' => 21.2217215,
                'lng' => -86.8029101,
            ],
            5 => [
                'lat' => 20.8471632,
                'lng' => -86.8803245,
            ],
            6 => [
                'lat' => 20.644799,
                'lng' => -87.0917467,
            ],
            7 => [
                'lat' => 21.0815015,
                'lng' => -86.8546508,
            ],
            8 => [
                'lat' => 20.5067138,
                'lng' => -87.2386847,
            ],
            9 => [
                'lat' => 20.4027428,
                'lng' => -87.3193673,
            ],
            10 => [
                'lat' => 20.214244,
                'lng' => -87.4559179,
            ],
            11 => [
                'lat' => 20.187102,
                'lng' => -87.443475,
            ],
            12 => [
                'lat' => 20.3618852,
                'lng' => -87.3327632,
            ],
            13 => [
                'lat' => 20.7612258,
                'lng' => -86.9612859,
            ],
            14 => [
                'lat' => 20.8704582,
                'lng' => -87.0702105,
            ],
            15 => [
                'lat' => 20.0311617,
                'lng' => -87.4780201,
            ],
            16 => [
                'lat' => 20.689586,
                'lng' => -88.2047133,
            ],
            17 => [
                'lat' => 21.4323185,
                'lng' => -87.3375753,
            ],
            18 => [
                'lat' => 20.6787816,
                'lng' => -88.5733424,
            ],
            19 => [
                'lat' => 20.9776327,
                'lng' => -89.6322621,
            ],
            20 => [
                'lat' => 18.526777,
                'lng' => -88.3300811,
            ],
            21 => [
                'lat' => 21.2440641,
                'lng' => -86.8119526,
            ],
            22 => [
                'lat' => 20.1695036,
                'lng' => -87.6847257,
            ],
            23 => [
                'lat' => 20.199593,
                'lng' => -87.49902,
            ],
        ];

        if( !isset($equivalences[$zone_id]) ) return ['lat' => '', 'lng' => ''];

        $lat = $equivalences[$zone_id]['lat'];
        $lng = $equivalences[$zone_id]['lng'];

        return ['lat' => $lat, 'lng' => $lng];
    }

    public function getMessages($id)
    {
        $xHTML  = '';

        $messages = DB::select("SELECT fup.id, fup.text, fup.type FROM reservations_follow_up as fup
                                 WHERE fup.type IN ('CLIENT','OPERATION') 
                                    AND fup.reservation_id = :id 
                                    AND fup.text IS NOT NULL 
                                    AND fup.text != '' ", ["id" => $id]);
        if( sizeof($messages) >= 1 ):
            foreach($messages as $key => $value):
                $xHTML .= '[('.$value->type.') '. $value->text.'] <br>';
            endforeach;
        endif;

        return $xHTML;
    }

    public function exportExcelBoard(Request $request)
    {
        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_duplicated = 0 AND rez.open_credit = 0 AND rez.is_quotation = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 00:00:00",
            'end' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 23:59:59",
        ];

        $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Rellenar con datos
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Código');
        $sheet->setCellValue('C1', 'Hora');
        $sheet->setCellValue('D1', 'Cliente');
        $sheet->setCellValue('E1', 'Tipo de servicios');
        $sheet->setCellValue('F1', 'Folio');
        $sheet->setCellValue('G1', 'Pax');
        $sheet->setCellValue('H1', 'Origen');
        $sheet->setCellValue('I1', 'Destino');
        $sheet->setCellValue('J1', 'Agencia');
        $sheet->setCellValue('K1', 'Unidad');
        $sheet->setCellValue('L1', 'Conductor');
        $sheet->setCellValue('M1', 'Estatus de operación');
        $sheet->setCellValue('N1', 'Hora de operación');
        $sheet->setCellValue('O1', 'Costo operativo');
        $sheet->setCellValue('P1', 'Estatus de reservación');
        $sheet->setCellValue('Q1', 'Vehículo');
        $sheet->setCellValue('R1', 'Estatus de pago');
        $sheet->setCellValue('S1', 'Total');
        $sheet->setCellValue('T1', 'Moneda');
        $sheet->setCellValue('U1', 'Metodo de pago');
        $sheet->setCellValue('V1', 'Tipo');
        $sheet->setCellValue('W1', 'Mensajes');

        $count = 2;

        foreach( $items as $key => $item ){
            // $payment = ( $item->total_sales - $item->total_payments );
            // if($payment < 0) $payment = 0;
            $payment = $item->total_sales;
            $messages_text = $this->getMessages($item->reservation_id);
            $message = ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_comments : $item->op_two_comments );
            if( !empty($message) ){
                $messages_text .= $message;
            }

            $flag_preassignment = ( ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ) && $item->op_one_preassignment != "" ? true : ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && ( $item->is_round_trip == 1 ) && $item->op_two_preassignment != "" ? true : false ) );
            $preassignment = ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_preassignment : $item->op_two_preassignment );

            $status_operation = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->one_service_operation_status : $item->two_service_operation_status );
            $time_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_time_operation : $item->op_two_time_operation );
            $cost_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_operating_cost : $item->op_two_operating_cost );
            $status_booking =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->one_service_status : $item->two_service_status );

            $operation_pickup = (($item->operation_type == 'arrival')? $item->pickup_from : $item->pickup_to );
            $operation_from = (($item->operation_type == 'arrival')? $item->from_name.((!empty($item->flight_number))? ' ('.$item->flight_number.')' :'')  : $item->to_name );
            $operation_to = (($item->operation_type == 'arrival')? $item->to_name : $item->from_name );
            $vehicle_d = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Vehicle::find($item->vehicle_id_one) : Vehicle::find($item->vehicle_id_two) );
            $driver_d =  ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Driver::find($item->driver_id_one) : Driver::find($item->driver_id_two) );


            $sheet->setCellValue('A'.strval($count), ( $preassignment != "" ? $preassignment : "ADD" ));
            $sheet->setCellValue('B'.strval($count), $item->code);
            $sheet->setCellValue('C'.strval($count), date("H:i", strtotime($operation_pickup)));
            $sheet->setCellValue('D'.strval($count), $item->full_name);
            $sheet->setCellValue('E'.strval($count), $item->final_service_type);
            $sheet->setCellValue('F'.strval($count), $item->reference);
            $sheet->setCellValue('G'.strval($count), $item->passengers);
            $sheet->setCellValue('H'.strval($count), $operation_from);
            $sheet->setCellValue('I'.strval($count), $operation_to);
            $sheet->setCellValue('J'.strval($count), $item->site_name);
            $sheet->setCellValue('K'.strval($count), ( isset($vehicle_d->name) ? $vehicle_d->name : 'Selecciona vehículo' ));
            $sheet->setCellValue('L'.strval($count), ( isset($driver_d->names) ? $driver_d->names.' '.$driver_d->surnames : 'Selecciona conductor' ));
            $sheet->setCellValue('M'.strval($count), $status_operation);
            $sheet->setCellValue('N'.strval($count), ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation);
            $sheet->setCellValue('O'.strval($count), $cost_operation);
            $sheet->setCellValue('P'.strval($count), $status_booking);
            $sheet->setCellValue('Q'.strval($count), $item->service_type_name);
            $sheet->setCellValue('R'.strval($count), $item->reservation_status);
            $sheet->setCellValue('S'.strval($count), number_format(( $item->is_round_trip == 1 ? ( $payment / 2 ) : $payment ),2));
            $sheet->setCellValue('T'.strval($count), $item->currency);
            $sheet->setCellValue('U'.strval($count), ( !empty($item->payment_type_name) ? $item->payment_type_name : "PENDIENTE DE PAGO" ));
            $sheet->setCellValue('V'.strval($count), ( $item->is_round_trip == 1 ? 'Round Trip' : 'One Way' ));
            $sheet->setCellValue('W'.strval($count), $messages_text);
            $count = $count + 1;
        }

        // Crear un escritor de archivos Excel
        $writer = new Xlsx($spreadsheet);

        // Crear una respuesta HTTP para la descarga del archivo
        $fileName = 'operation_board_'.$request->date.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return ResponseFile::download($temp_file, $fileName)->deleteFileAfterSend(true);        
    }

    public function exportExcelBoardCommision(Request $request)
    {
        $queryOne = " AND it.op_one_pickup BETWEEN :init_date_one AND :init_date_two AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 ";
        $queryTwo = " AND it.op_two_pickup BETWEEN :init_date_three AND :init_date_four AND rez.is_cancelled = 0 AND rez.is_duplicated = 0 AND it.is_round_trip = 1 ";
        $havingConditions = []; $queryHaving = "";
        $queryData = [
            'init' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 00:00:00",
            'end' => ( isset( $request->date ) ? $request->date : date("Y-m-d") ) ." 23:59:59",
        ];

        $items = $this->queryOperations($queryOne, $queryTwo, $queryHaving, $queryData);
        //  

        // Crear una nueva hoja de cálculo
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Rellenar con datos
        $sheet->setCellValue('A1', '#');
        $sheet->setCellValue('B1', 'Hora');
        $sheet->setCellValue('C1', 'Cliente');
        $sheet->setCellValue('D1', 'Tipo de servicios');
        $sheet->setCellValue('E1', 'Folio');
        $sheet->setCellValue('F1', 'Pax');
        $sheet->setCellValue('G1', 'Origen');
        $sheet->setCellValue('H1', 'Destino');
        $sheet->setCellValue('I1', 'Agencia');
        $sheet->setCellValue('J1', 'Unidad');
        $sheet->setCellValue('K1', 'Conductor');
        $sheet->setCellValue('L1', 'Hora de operación');
        $sheet->setCellValue('M1', 'Costo operativo');
        $sheet->setCellValue('N1', 'Total');
        $sheet->setCellValue('O1', 'Comisión');
        $sheet->setCellValue('P1', 'Monenda Comisión');

        $count = 2;

        foreach( $items as $key => $item ){
            $payment = ( $item->site_id == 21 ? ( $item->currency == "USD" ? ( $item->total_sales * 16 ) : $item->total_sales ) : $item->total_sales );

            $preassignment = ( ( $item->final_service_type == 'ARRIVAL' ) || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_preassignment : $item->op_two_preassignment );

            $status_operation = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->one_service_operation_status : $item->two_service_operation_status );
            $time_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_time_operation : $item->op_two_time_operation );
            $cost_operation =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->op_one_operating_cost : $item->op_two_operating_cost );
            $status_booking =   ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? $item->one_service_status : $item->two_service_status );            

            $operation_pickup = (($item->operation_type == 'arrival')? $item->pickup_from : $item->pickup_to );
            $operation_from = (($item->operation_type == 'arrival')? $item->from_name.((!empty($item->flight_number))? ' ('.$item->flight_number.')' :'')  : $item->to_name );
            $operation_to = (($item->operation_type == 'arrival')? $item->to_name : $item->from_name );
            $vehicle_d = ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Vehicle::find($item->vehicle_id_one) : Vehicle::find($item->vehicle_id_two) );
            $driver_d =  ( ($item->final_service_type == 'ARRIVAL') || ( ( $item->final_service_type == 'TRANSFER' || $item->final_service_type == 'DEPARTURE' ) && $item->op_type == "TYPE_ONE" && ( $item->is_round_trip == 0 || $item->is_round_trip == 1 ) ) ? Driver::find($item->driver_id_one) : Driver::find($item->driver_id_two) );
            $porcentaje = ( $item->site_id == 21 ? 0.04 : 0.05 );
            $commission = ( $item->site_id == 21 ? ( $payment * $porcentaje ) : ( $cost_operation * $porcentaje ) );

            $sheet->setCellValue('A'.strval($count), ( $preassignment != "" ? $preassignment : "ADD" ));
            $sheet->setCellValue('B'.strval($count), date("H:i", strtotime($operation_pickup)));
            $sheet->setCellValue('C'.strval($count), $item->full_name);
            $sheet->setCellValue('D'.strval($count), $item->final_service_type);
            $sheet->setCellValue('E'.strval($count), $item->reference);
            $sheet->setCellValue('F'.strval($count), $item->passengers);
            $sheet->setCellValue('G'.strval($count), $operation_from);
            $sheet->setCellValue('H'.strval($count), $operation_to);
            $sheet->setCellValue('I'.strval($count), $item->site_name);
            $sheet->setCellValue('J'.strval($count), ( isset($vehicle_d->name) ? $vehicle_d->name : 'Selecciona vehículo' ));
            $sheet->setCellValue('K'.strval($count), ( isset($driver_d->names) ? $driver_d->names.' '.$driver_d->surnames : 'Selecciona conductor' ));
            $sheet->setCellValue('L'.strval($count), ( $time_operation != NULL )  ? date("H:i", strtotime($time_operation)) : $time_operation);
            $sheet->setCellValue('M'.strval($count), number_format($cost_operation,2));
            $sheet->setCellValue('N'.strval($count), number_format($payment,2));
            $sheet->setCellValue('O'.strval($count), $commission);
            $sheet->setCellValue('P'.strval($count), 'MXN');
            $count = $count + 1;
        }

        // Crear un escritor de archivos Excel
        $writer = new Xlsx($spreadsheet);

        // Crear una respuesta HTTP para la descarga del archivo
        $fileName = 'operation_board_comission_'.$request->date.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($temp_file);

        return ResponseFile::download($temp_file, $fileName)->deleteFileAfterSend(true);        
    }

    public function getSchedules(Request $request)
    {
        $schedules = DriverSchedule::where('date', ( isset($request->date) ? $request->date : date('Y-m-d') ))
                                    ->orderBy('date', 'ASC')
                                    ->orderBy('check_in_time', 'ASC')
                                    ->get();

        return view('components.html.management.operations.schedules', [ 'schedules' => $schedules ]);
    }

    public function updateSchedules(Request $request)
    {
        // Reglas de validación
        $rules = [
            'code' => 'required|integer',
            'type' => 'required|stringer|in:end_check_out_time,vehicle,driver,status,observations',
        ];

        // Validación de datos
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'required_params', 
                    'message' =>  $validator->errors()->all() 
                ],
                'status' => 'error',
                'message' => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        // Obtener el item de la reservación
        $schedule = DriverSchedule::where('id', $request->code)->first();
        
        if (!$schedule) {
            return response()->json([
                'errors' => [
                    'code' => 'not_found', 
                    'message' =>  "Horario no encontrado" 
                ],
                'status' => 'error',
                'message' => 'Horario no encontrado'
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            DB::beginTransaction();

            switch ($request->type) {
                case 'end_check_out_time':
                        if (!empty($request->value)) {
                            $time_in = Carbon::createFromFormat('H:i:s', $schedule->check_out_time);
                            $time_out = Carbon::createFromFormat('H:i:s', $request->value.':00');
                            $difference = $time_in->diff($time_out);
                            $schedule->end_check_out_time = $request->value;
                            // Asigna el valor si hay diferencia, de lo contrario deja null
                            if ($difference->h != 0 || $difference->i != 0) {
                                $schedule->extra_hours = sprintf('%02d:%02d:00', $difference->h, $difference->i);
                            } else {
                                $schedule->extra_hours = null;
                            }
                        }
                    break;
                case 'vehicle':
                        $schedule->vehicle_id = ($request->value ?? 0) != 0 ? $request->value : NULL;                    
                    break;
                case 'driver':
                        $schedule->driver_id = ($request->value ?? 0) != 0 ? $request->value : NULL;
                    break;
                case 'status':
                        $schedule->status = $request->value ?? NULL;
                    break;
                default:
                        $schedule->observations = $request->value ?? NULL;
                    break;
            }

            // Guardar el cambio y verificar que se guardó correctamente
            if (!$schedule->save()) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar el horario.'
                ], Response::HTTP_INTERNAL_SERVER_ERROR);
            }            

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Se actualizo correctamente el horario.',
            ], Response::HTTP_OK);            
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getReservation($id)
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
            'site',
            'sales',
            'payments',
            'refunds',
            'followUps' => function ($query) {
                $query->whereIn('type', ['CLIENT', 'OPERATION']);
            },
            'photos',
            'cancellationType',
            'originSale'
        ])->find($id);

        return $reservation;
    }
}