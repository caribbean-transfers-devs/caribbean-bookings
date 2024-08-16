<?php

namespace App\Repositories\Dashboards;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelIgnition\Recorders\DumpRecorder\Dump;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;

class DashboardRepository
{
    public function index(){
        return view('dashboard.default');
    }

    public function admin(){
        
        $bookings_month = [];
        $queryData = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 ';

        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $bookings_month[$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];
        }

        $bookings_data_month = $this->dataBooking($query, $queryData);

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):                
                $date_ = date("Y-m-d", strtotime( $value->created_at ));
                if( isset( $bookings_month[ $date_ ] ) ){
                    $bookings_month[ $date_ ]['items'][] = $value;
                    $bookings_month[ $date_ ]['counter']++;
                    if( $value->currency == "USD" ):
                        $bookings_month[ $date_ ]['USD'] += $value->total_sales;
                    endif;
                    if( $value->currency == "MXN" ):
                        $bookings_month[ $date_ ]['MXN'] += $value->total_sales;
                    endif;                   
                }
            endforeach;
        }
        
        return view('dashboard.admin', ['items' => $bookings_month]);
    }
    
    public function sales($request, $type){

        $bookings_day = [
            "USD" => [
                "total" => 0,
                "counter" => 0
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0
            ],
            "counter" => 0,
            "bookings" => [],
            "bookings_day" => [],
            "status" => [
                "confirmed" => [
                    "title" => "Confirmadas",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                    "percentage" => 0
                ],
                "pending" => [
                    "title" => "Pendientes",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                    "percentage" => 0
                ],
                "canceled" => [
                    "title" => "Canceladas",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                    "percentage" => 0
                ],                
            ],            
        ];//NOS AYUDA A SABER LAS ESTADISTICAS DE VENTAS DEL DIA
        $bookings_month = [
            "USD" => [
                "total" => 0,
                "counter" => 0
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0
            ],
            "counter" => 0,
            "bookings" => [],
            "bookings_day" => [],
            "status" => [
                "confirmed" => [
                    "title" => "Confirmadas",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                    "percentage" => 0
                ],
                "pending" => [
                    "title" => "Pendientes",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                    "percentage" => 0
                ],
                "canceled" => [
                    "title" => "Canceladas",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                    "percentage" => 0
                ],
            ],
        ];//NOS AYUDA A SABER LAS ESTADISTICAS DE VENTAS DEL MES
        $bookings_sites_day = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];        
        $bookings_sites_month = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];

        $bookings_destinations_day = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];        
        $bookings_destinations_month = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];

        $data = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB
        if( $type == "general" ){
            $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_duplicated <> 1 ';
        }
        if( $type == "online" ){
            $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_duplicated <> 1 AND rez.site_id NOT IN (11,21) ';
        }
        if( $type == "airport" ){
            $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_duplicated <> 1 AND rez.site_id IN (11,21) ';
        }

        $fecha1 = date('m', strtotime(( isset( $request->date ) && !empty( $request->date ) ? explode(" - ", $request->date)[0] : date("Y-m-d") )));
        $fecha2 = date('m', strtotime(date("Y-m-d")));
        // Obtener el mes de cada fecha
        // $mes1 = $fecha1->format('m');
        // $mes2 = $fecha2->format('m');
        $flag_month = ( $fecha1 !== $fecha2 ? true : false );        
        
        $queryDataDay = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
        ];
        $queryDataMonth = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];

        $bookings_day["bookings_day"][date("Y-m-d")] = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "bookings" => [],
        ];

        $tmp_date = ( isset( $request->date ) && !empty( $request->date ) ? explode(" - ", $request->date) : array() );
        $data['init'] = ( isset($tmp_date[0]) ? $tmp_date[0] : date("Y-m-d", strtotime("first day of this month")) );
        $data['end'] = ( isset($tmp_date[1]) ? $tmp_date[1] : date("Y-m-d", strtotime("last day of this month")) );
        $queryDataMonth['init'] =  ( isset($tmp_date[0]) ? $tmp_date[0] : date("Y-m-d", strtotime("first day of this month")) ).' 00:00:00';
        $queryDataMonth['end'] = ( isset($tmp_date[1]) ? $tmp_date[1] : date("Y-m-d", strtotime("last day of this month")) ).' 23:59:59';
        // Recorre desde el primer día hasta el último día del mes
        for ($fecha = date("Y-m-d", strtotime(( isset($tmp_date[0]) ? $tmp_date[0] : date("Y-m-d", strtotime("first day of this month")) ))); $fecha <= date("Y-m-d", strtotime(( isset($tmp_date[1]) ? $tmp_date[1] : date("Y-m-d", strtotime("last day of this month")) ))); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
            $bookings_month["bookings_day"][$fecha] = [
                "items" => [],
                "counter" => 0,
                "USD" => 0,
                "MXN" => 0,
            ];
        }

        $bookings_data_day = $this->dataBooking($query, $queryDataDay);
        $bookings_data_month = $this->dataBooking($query, $queryDataMonth);

        if(sizeof( $bookings_data_day ) >= 1){
            foreach($bookings_data_day as $bookingsDay):
                // $bookingsDay->status = ( $bookingsDay->pay_at_arrival == 1 || $bookingsDay->status == "CONFIRMED"  ? "CONFIRMED" : $bookingsDay->status ) ;//MODIFICAMOS EL TEXTO DE LOS ESTATUS EN BASE AL IDIOMA
                // $bookingsDay->status = ( $bookingsDay->is_cancelled == 1 ? "CANCELED" : ( ($bookingsDay->pay_at_arrival == 0 && $bookingsDay->is_cancelled == 0 && $bookingsDay->status == "PENDING") ? "PENDING" : "CONFIRMED" ) ) ;//MODIFICAMOS EL TEXTO DE LOS ESTATUS EN BASE AL IDIOMA

                if( ( $bookingsDay->is_cancelled == 0 && ($bookingsDay->pay_at_arrival == 0 || $bookingsDay->pay_at_arrival == 1) && $bookingsDay->status == "CONFIRMED" ) || ( $bookingsDay->is_cancelled == 0 && $bookingsDay->pay_at_arrival == 1 && $bookingsDay->status == "PENDING" ) ){
                    $bookingsDay->status = "CONFIRMED";
                }

                if( ( $bookingsDay->pay_at_arrival == 0 && $bookingsDay->is_cancelled == 0 && $bookingsDay->status == "PENDING" ) ){
                    $bookingsDay->status = "PENDING";
                }

                if( $bookingsDay->is_cancelled == 1 && ( $bookingsDay->pay_at_arrival == 0 || $bookingsDay->pay_at_arrival == 1 ) ){
                    $bookingsDay->status = "CANCELED";
                }

                $date_ = date("Y-m-d", strtotime( $bookingsDay->created_at ));

                $bookings_day[$bookingsDay->currency]["total"] += $bookingsDay->total_sales;
                $bookings_day[$bookingsDay->currency]["counter"]++;
                $bookings_day['counter']++;
                $bookings_day["bookings"][] = $bookingsDay;

                if( isset( $bookings_day["bookings_day"][ $date_ ] ) ){
                    if( ( $bookingsDay->status == "CONFIRMED" || $bookingsDay->status == "PENDING" ) ){
                        $bookings_day["bookings_day"][ $date_ ]['bookings_day'][] = $bookingsDay;
                        $bookings_day["bookings_day"][ $date_ ]['counter']++;
                        $bookings_day["bookings_day"][ $date_ ][$bookingsDay->currency] += $bookingsDay->total_sales;
                    }
                }

                if( isset( $bookings_day["status"][ Str::slug($bookingsDay->status) ] ) ){  
                    $bookings_day["status"][ Str::slug($bookingsDay->status) ][$bookingsDay->currency] += $bookingsDay->total_sales;
                    $bookings_day["status"][ Str::slug($bookingsDay->status) ]['counter']++;
                    $bookings_day["status"][ Str::slug($bookingsDay->status) ]['percentage'] = ($bookings_day["status"][ Str::slug($bookingsDay->status) ]['counter'] / $bookings_day["counter"]) * 100;
                }

                if( ( $bookingsDay->status == "CONFIRMED" || $bookingsDay->status == "PENDING" ) ){
                    //ALIMENTAMOS LAS VENTAS DEL MES POR SITIO
                    if(!isset( $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)] )):
                        $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)] = [
                            'name' => '',
                            'USD' => 0,
                            'MXN' => 0,                        
                            'counter' => 0                        
                        ];
                    endif;
                    $bookings_sites_day[$bookingsDay->currency] += $bookingsDay->total_sales;
                    $bookings_sites_day['counter']++;
                    $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)]['name'] = $bookingsDay->site_name;
                    $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)][$bookingsDay->currency] += $bookingsDay->total_sales;
                    $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)]['counter']++;

                    //ALIMENTAMOS LAS VENTAS DEL MES POR DESTINO
                    if(!isset( $bookings_destinations_day['data'][Str::slug($bookingsDay->destination_name)] )):
                        $faker = Faker::create();
                        $cadenaAleatoria = $faker->regexify('[A-F0-9]{6}');
                        $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))] = [
                            'name' => '',
                            'USD' => 0,
                            'MXN' => 0,
                            'counter' => 0,
                            'color' => '#' . $cadenaAleatoria
                        ];
                    endif;
                    $bookings_destinations_day[$bookingsDay->currency] += $bookingsDay->total_sales;
                    $bookings_destinations_day['counter']++;
                    $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))]['name'] = ($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido");
                    $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))][$bookingsDay->currency] += $bookingsDay->total_sales;
                    $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))]['counter']++;
                }

            endforeach;
        }

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):
                // $value->status = (( $value->pay_at_arrival == 1 && $value->is_cancelled == 0 ) || $value->status == "CONFIRMED"  ? "CONFIRMED" : ( $value->pay_at_arrival == 0 && $value->is_cancelled == 0 ? "PENDING" : "CANCELED" ) ) ;//MODIFICAMOS EL TEXTO DE LOS ESTATUS EN BASE AL IDIOMA
                // $value->status = ( $value->is_cancelled == 1 ? "CANCELED" : ( ($value->pay_at_arrival == 0 && $value->is_cancelled == 0 && $value->status == "PENDING") ? "PENDING" : "CONFIRMED" ) ) ;//MODIFICAMOS EL TEXTO DE LOS ESTATUS EN BASE AL IDIOMA

                if( ( $value->is_cancelled == 0 && ($value->pay_at_arrival == 0 || $value->pay_at_arrival == 1) && $value->status == "CONFIRMED" ) || ( $value->is_cancelled == 0 && $value->pay_at_arrival == 1 && $value->status == "PENDING" ) ){
                    $value->status = "CONFIRMED";
                }

                if( ( $value->pay_at_arrival == 0 && $value->is_cancelled == 0 && $value->status == "PENDING" ) ){
                    $value->status = "PENDING";
                }

                if( $value->is_cancelled == 1 && ( $value->pay_at_arrival == 0 || $value->pay_at_arrival == 1 ) ){
                    $value->status = "CANCELED";
                }

                $date_ = date("Y-m-d", strtotime( $value->created_at ));

                $bookings_month[$value->currency]["total"] += $value->total_sales;
                $bookings_month[$value->currency]["counter"]++;
                $bookings_month['counter']++;
                $bookings_month["bookings"][] = $value;

                if( isset( $bookings_month["bookings_day"][ $date_ ] ) ){
                    if( ( $value->status == "CONFIRMED" || $value->status == "PENDING" ) ){
                        $bookings_month["bookings_day"][ $date_ ]['bookings_day'][] = $value;
                        $bookings_month["bookings_day"][ $date_ ]['counter']++;
                        $bookings_month["bookings_day"][ $date_ ][$value->currency] += $value->total_sales;
                    }
                }

                if( isset( $bookings_month["status"][ Str::slug($value->status) ] ) ){
                    $bookings_month["status"][ Str::slug($value->status) ][$value->currency] += $value->total_sales;
                    $bookings_month["status"][ Str::slug($value->status) ]['counter']++;
                    $bookings_month["status"][ Str::slug($value->status) ]['percentage'] = ($bookings_month["status"][ Str::slug($value->status) ]['counter'] / $bookings_month["counter"]) * 100;
                }

                if( ( $value->status == "CONFIRMED" || $value->status == "PENDING" ) ){
                    //ALIMENTAMOS LAS VENTAS DEL MES POR SITIO
                    if(!isset( $bookings_sites_month['data'][Str::slug($value->site_name)] )):
                        $bookings_sites_month['data'][Str::slug($value->site_name)] = [
                            'name' => '',
                            'USD' => 0,
                            'MXN' => 0,                        
                            'counter' => 0                        
                        ];
                    endif;
                    $bookings_sites_month[$value->currency] += $value->total_sales;
                    $bookings_sites_month['counter']++;
                    $bookings_sites_month['data'][Str::slug($value->site_name)]['name'] = $value->site_name;
                    $bookings_sites_month['data'][Str::slug($value->site_name)][$value->currency] += $value->total_sales;
                    $bookings_sites_month['data'][Str::slug($value->site_name)]['counter']++;

                    //ALIMENTAMOS LAS VENTAS DEL MES POR DESTINO
                    if(!isset( $bookings_destinations_month['data'][Str::slug($value->destination_name)] )):
                        $faker = Faker::create();
                        $cadenaAleatoria = $faker->regexify('[A-F0-9]{6}');
                        $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))] = [
                            'name' => '',
                            'USD' => 0,
                            'MXN' => 0,
                            'counter' => 0,
                            'color' => '#' . $cadenaAleatoria
                        ];
                    endif;
                    $bookings_destinations_month[$value->currency] += $value->total_sales;
                    $bookings_destinations_month['counter']++;
                    $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))]['name'] = ($value->destination_name != "" ? $value->destination_name : "Indefinido");
                    $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))][$value->currency] += $value->total_sales;
                    $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))]['counter']++;
                }
                
            endforeach;
        }
        
        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => ( $type == "online" ? "Ventas en linea" : ( $type == "online" ? "Ventas de Aereopuerto" : "Ventas generales" ) ),
                "active" => true
            ),
        );

        return view('dashboard.Nsales', [
            'bookings_day' => $bookings_day, 
            'bookings_sites_day' => $bookings_sites_day,
            'bookings_destinations_day' => $bookings_destinations_day,
            'bookings_month' => $bookings_month,
            'bookings_sites_month' => $bookings_sites_month,
            'bookings_destinations_month' => $bookings_destinations_month,
            'data' => $data,
            'flag_month' => $flag_month,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function sales2($request, $type){

        $bookings_day = [
            "USD" => [
                "total" => 0,
                "counter" => 0
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0
            ],
            "counter" => 0,
            "bookings" => [],
            "bookings_day" => [],
            "status" => [
                "confirmed" => [
                    "title" => "Confirmed",
                    "color" => "#00ab55",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                ],
                "pending" => [
                    "title" => "Pending",
                    "color" => "#e2a03f",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                ],                    
            ],            
        ];//NOS AYUDA A SABER LAS ESTADISTICAS DE VENTAS DEL DIA
        $bookings_month = [
            "USD" => [
                "total" => 0,
                "counter" => 0
            ],
            "MXN" => [
                "total" => 0,
                "counter" => 0
            ],
            "counter" => 0,
            "bookings" => [],
            "bookings_day" => [],
            "status" => [
                "confirmed" => [
                    "title" => "Confirmed",
                    "color" => "#00ab55",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                ],
                "pending" => [
                    "title" => "Pending",
                    "color" => "#e2a03f",
                    "USD" => 0,
                    "MXN" => 0,
                    "counter" => 0,
                ],
            ],
        ];//NOS AYUDA A SABER LAS ESTADISTICAS DE VENTAS DEL MES
        $bookings_sites_day = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];        
        $bookings_sites_month = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];

        $bookings_destinations_day = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];        
        $bookings_destinations_month = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "data" => [],
        ];

        $data = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];
        
        //Query DB        
        if( $type == "general" ){
            $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 ';
        }
        if( $type == "online" ){
            $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 AND rez.site_id NOT IN (11,21) ';
        }
        if( $type == "airport" ){
            $query = ' AND rez.created_at BETWEEN :init AND :end AND rez.is_cancelled <> 1 AND rez.is_duplicated <> 1 AND rez.site_id IN (11,21) ';
        }

        $queryDataDay = [
            "init" => date("Y-m-d") . " 00:00:00",
            "end" => date("Y-m-d") . " 23:59:59",
        ];        
        $queryDataMonth = [
            "init" => date("Y-m-d", strtotime("first day of this month")) . " 00:00:00",
            "end" => date("Y-m-d", strtotime("last day of this month")) . " 23:59:59",
        ];

        $bookings_day["bookings_day"][date("Y-m-d")] = [
            "USD" => 0,
            "MXN" => 0,
            "counter" => 0,
            "bookings" => [],
        ];

        if(isset( $request->date ) && !empty( $request->date )){
            $tmp_date = explode(" - ", $request->date);
            $data['init'] = $tmp_date[0];
            $data['end'] = $tmp_date[1];
            $queryDataMonth['init'] = $tmp_date[0].' 00:00:00';
            $queryDataMonth['end'] = $tmp_date[1].' 23:59:59';
            // Recorre desde el primer día hasta el último día del mes
            for ($fecha = date("Y-m-d", strtotime($tmp_date[0])); $fecha <= date("Y-m-d", strtotime($tmp_date[1])); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
                $bookings_month["bookings_day"][$fecha] = [
                    "items" => [],
                    "counter" => 0,
                    "USD" => 0,
                    "MXN" => 0,
                ];
            }
        }else{
            // Recorre desde el primer día hasta el último día del mes
            for ($fecha = date("Y-m-d", strtotime("first day of this month")); $fecha <= date("Y-m-d", strtotime("last day of this month")); $fecha = date("Y-m-d", strtotime($fecha . " +1 day"))) {
                $bookings_month["bookings_day"][$fecha] = [
                    "items" => [],
                    "counter" => 0,
                    "USD" => 0,
                    "MXN" => 0,
                ];
            }
        }

        $bookings_data_day = $this->dataBooking($query, $queryDataDay);
        $bookings_data_month = $this->dataBooking($query, $queryDataMonth);

        if(sizeof( $bookings_data_day ) >= 1){
            foreach($bookings_data_day as $bookingsDay):
                $bookingsDay->status = ( $bookingsDay->pay_at_arrival == 1 || $bookingsDay->status == "CONFIRMED"  ? "CONFIRMED" : $bookingsDay->status ) ;//MODIFICAMOS EL TEXTO DE LOS ESTATUS EN BASE AL IDIOMA
                $date_ = date("Y-m-d", strtotime( $bookingsDay->created_at ));

                $bookings_day[$bookingsDay->currency]["total"] += $bookingsDay->total_sales;
                $bookings_day[$bookingsDay->currency]["counter"]++;
                $bookings_day['counter']++;
                $bookings_day["bookings"][] = $bookingsDay;

                if( isset( $bookings_day["bookings_day"][ $date_ ] ) ){
                    $bookings_day["bookings_day"][ $date_ ]['bookings_day'][] = $bookingsDay;
                    $bookings_day["bookings_day"][ $date_ ]['counter']++;
                    $bookings_day["bookings_day"][ $date_ ][$bookingsDay->currency] += $bookingsDay->total_sales;
                }

                if( isset( $bookings_day["status"][ Str::slug($bookingsDay->status) ] ) ){  
                    $bookings_day["status"][ Str::slug($bookingsDay->status) ][$bookingsDay->currency] += $bookingsDay->total_sales;
                    $bookings_day["status"][ Str::slug($bookingsDay->status) ]['counter']++;
                }

                //ALIMENTAMOS LAS VENTAS DEL MES POR SITIO
                if(!isset( $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)] )):
                    $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)] = [
                        'name' => '',
                        'USD' => 0,
                        'MXN' => 0,                        
                        'counter' => 0                        
                    ];
                endif;
                $bookings_sites_day[$bookingsDay->currency] += $bookingsDay->total_sales;
                $bookings_sites_day['counter']++;
                $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)]['name'] = $bookingsDay->site_name;
                $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)][$bookingsDay->currency] += $bookingsDay->total_sales;
                $bookings_sites_day['data'][Str::slug($bookingsDay->site_name)]['counter']++;

                //ALIMENTAMOS LAS VENTAS DEL MES POR DESTINO
                if(!isset( $bookings_destinations_day['data'][Str::slug($bookingsDay->destination_name)] )):
                    $faker = Faker::create();
                    $cadenaAleatoria = $faker->regexify('[A-F0-9]{6}');
                    $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))] = [
                        'name' => '',
                        'USD' => 0,
                        'MXN' => 0,
                        'counter' => 0,
                        'color' => '#' . $cadenaAleatoria
                    ];
                endif;
                $bookings_destinations_day[$bookingsDay->currency] += $bookingsDay->total_sales;
                $bookings_destinations_day['counter']++;
                $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))]['name'] = ($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido");
                $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))][$bookingsDay->currency] += $bookingsDay->total_sales;
                $bookings_destinations_day['data'][Str::slug(($bookingsDay->destination_name != "" ? $bookingsDay->destination_name : "Indefinido"))]['counter']++;

            endforeach;
        }

        if(sizeof( $bookings_data_month ) >= 1){
            foreach($bookings_data_month as $value):
                $value->status = ( $value->pay_at_arrival == 1 || $value->status == "CONFIRMED"  ? "CONFIRMED" : $value->status ) ;//MODIFICAMOS EL TEXTO DE LOS ESTATUS EN BASE AL IDIOMA
                $date_ = date("Y-m-d", strtotime( $value->created_at ));

                $bookings_month[$value->currency]["total"] += $value->total_sales;
                $bookings_month[$value->currency]["counter"]++;
                $bookings_month['counter']++;
                $bookings_month["bookings"][] = $value;

                if( isset( $bookings_month["bookings_day"][ $date_ ] ) ){
                    $bookings_month["bookings_day"][ $date_ ]['bookings_day'][] = $value;
                    $bookings_month["bookings_day"][ $date_ ]['counter']++;
                    $bookings_month["bookings_day"][ $date_ ][$value->currency] += $value->total_sales;
                }

                if( isset( $bookings_month["status"][ Str::slug($value->status) ] ) ){
                    $bookings_month["status"][ Str::slug($value->status) ][$value->currency] += $value->total_sales;
                    $bookings_month["status"][ Str::slug($value->status) ]['counter']++;
                }                

                //ALIMENTAMOS LAS VENTAS DEL MES POR SITIO
                if(!isset( $bookings_sites_month['data'][Str::slug($value->site_name)] )):
                    $bookings_sites_month['data'][Str::slug($value->site_name)] = [
                        'name' => '',
                        'USD' => 0,
                        'MXN' => 0,                        
                        'counter' => 0                        
                    ];
                endif;
                $bookings_sites_month[$value->currency] += $value->total_sales;
                $bookings_sites_month['counter']++;
                $bookings_sites_month['data'][Str::slug($value->site_name)]['name'] = $value->site_name;
                $bookings_sites_month['data'][Str::slug($value->site_name)][$value->currency] += $value->total_sales;
                $bookings_sites_month['data'][Str::slug($value->site_name)]['counter']++;

                //ALIMENTAMOS LAS VENTAS DEL MES POR DESTINO
                if(!isset( $bookings_destinations_month['data'][Str::slug($value->destination_name)] )):
                    $faker = Faker::create();
                    $cadenaAleatoria = $faker->regexify('[A-F0-9]{6}');
                    $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))] = [
                        'name' => '',
                        'USD' => 0,
                        'MXN' => 0,
                        'counter' => 0,
                        'color' => '#' . $cadenaAleatoria
                    ];
                endif;
                $bookings_destinations_month[$value->currency] += $value->total_sales;
                $bookings_destinations_month['counter']++;
                $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))]['name'] = ($value->destination_name != "" ? $value->destination_name : "Indefinido");
                $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))][$value->currency] += $value->total_sales;
                $bookings_destinations_month['data'][Str::slug(($value->destination_name != "" ? $value->destination_name : "Indefinido"))]['counter']++;
                
            endforeach;
        }
        
        $breadcrumbs = array(
            array(
                "route" => "",
                "name" => ( $type == "online" ? "Ventas en linea" : ( $type == "online" ? "Ventas de Aereopuerto" : "Ventas generales" ) ),
                "active" => true
            ),
        );

        return view('dashboard.sales', [
            'bookings_day' => $bookings_day, 
            'bookings_sites_day' => $bookings_sites_day,
            'bookings_destinations_day' => $bookings_destinations_day,
            'bookings_month' => $bookings_month,
            'bookings_sites_month' => $bookings_sites_month,
            'bookings_destinations_month' => $bookings_destinations_month,
            'data' => $data,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }    

    public function dataBooking($query, $queryData){
        return DB::select("SELECT 
                            rez.id, rez.created_at, CONCAT(rez.client_first_name,' ',rez.client_last_name) as client_full_name, rez.client_email, rez.currency, rez.is_cancelled, rez.is_duplicated, rez.affiliate_id, 
                            rez.pay_at_arrival,
                            COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                            CASE
                                WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDING'
                                ELSE 'CONFIRMED'
                            END AS status,
                            site.name as site_name,
                            GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS reservation_codes,
                            GROUP_CONCAT(DISTINCT it.zone_two_name ORDER BY it.zone_two_name ASC SEPARATOR ',') AS destination_name,
                            GROUP_CONCAT(DISTINCT it.zone_two_id ORDER BY it.zone_two_id ASC SEPARATOR ',') AS zone_two_id,
                            GROUP_CONCAT(DISTINCT it.service_type_id ORDER BY it.service_type_id ASC SEPARATOR ',') AS service_type_id,
                            GROUP_CONCAT(DISTINCT it.service_type_name ORDER BY it.service_type_name ASC SEPARATOR ',') AS service_type_name,
                            SUM(it.passengers) as passengers,
                            GROUP_CONCAT(DISTINCT p.payment_type_name ORDER BY p.payment_type_name ASC SEPARATOR ', ') AS payment_type_name,
                            COALESCE(SUM(it.op_one_pickup_today) + SUM(it.op_one_pickup_today), 0) as is_today                                     
                        FROM reservations as rez
                            INNER JOIN sites as site ON site.id = rez.site_id
                            LEFT JOIN (
                                SELECT reservation_id,  ROUND( COALESCE(SUM(total), 0), 2) as total_sales
                                FROM sales
                                WHERE deleted_at IS NULL
                                GROUP BY reservation_id
                            ) as s ON s.reservation_id = rez.id
                            LEFT JOIN (
                                SELECT reservation_id,
                                ROUND(SUM(CASE WHEN operation = 'multiplication' THEN total * exchange_rate
                                                            WHEN operation = 'division' THEN total / exchange_rate
                                                    ELSE total END), 2) AS total_payments,
                                GROUP_CONCAT(DISTINCT payment_method ORDER BY payment_method ASC SEPARATOR ',') AS payment_type_name
                                FROM payments
                                GROUP BY reservation_id
                            ) as p ON p.reservation_id = rez.id
                            LEFT JOIN (
                                SELECT  it.reservation_id, it.is_round_trip,
                                        SUM(it.passengers) as passengers,
                                        GROUP_CONCAT(DISTINCT it.code ORDER BY it.code ASC SEPARATOR ',') AS code,
                                        GROUP_CONCAT(DISTINCT zone_two.name ORDER BY zone_two.name ASC SEPARATOR ',') AS zone_two_name, 
                                        GROUP_CONCAT(DISTINCT zone_two.id ORDER BY zone_two.id ASC SEPARATOR ',') AS zone_two_id, 
                                        GROUP_CONCAT(DISTINCT dest.id ORDER BY dest.id ASC SEPARATOR ',') AS service_type_id, 
                                        GROUP_CONCAT(DISTINCT dest.name ORDER BY dest.name ASC SEPARATOR ',') AS service_type_name,
                                        MAX(CASE WHEN DATE(it.op_one_pickup) = CURDATE() THEN 1 ELSE 0 END) AS op_one_pickup_today,
                                        MAX(CASE WHEN DATE(it.op_two_pickup) = CURDATE() THEN 1 ELSE 0 END) AS op_two_pickup_today
                                FROM reservations_items as it
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
                                INNER JOIN destination_services as dest ON dest.id = it.destination_service_id
                                GROUP BY it.reservation_id, it.is_round_trip
                            ) as it ON it.reservation_id = rez.id
                        WHERE 1=1 {$query}
                        GROUP BY rez.id, site.name",
                            $queryData);
    }   
}