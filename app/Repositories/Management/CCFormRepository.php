<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Models\ReservationsItem;
use App\Models\ReservationFollowUp;
use Codedge\Fpdf\Fpdf\Fpdf;

class CCFormRepository
{
    public function index($request){
        return view('management.ccform.index', [
            'search' => [
                "init_date" => date("Y-m-d"),
                "end_date" => date("Y-m-d")
            ],
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Descarga de CCForm",
                    "active" => true                    
                ]
            ]
        ]);
    }

    public function createPDF($request)
    {    
        $search = [
            "init_date" => date("Y-m-d")." 00:00:00",
            "end_date" => date("Y-m-d")." 23:59:59",
            "type" => "arrival"
        ];

        if(isset( $request->date )):
            $new_date = explode(" - ", $request->date);
            $search['init_date'] = $new_date[0]." 00:00:00";
            $search['end_date'] = $new_date[1]." 23:59:59"; 
        endif;

        if(isset( $request->type )):
            $search['type'] = $request->type;
        endif;

        if(isset( $request->date )):
            $data = $this->QueryCCForm($search);
        endif;

        if(isset( $request->id )):
            $data = $this->QueryCCFormId($request->id);
        endif;

        if(empty($data)){
            exit;
        }
        
        $ids = [];
        $payments_with_errors = [];
        $payments_with_success = [];
                
        if($search['type'] == "arrival"):
            $filtered_data = array_filter($data, function($item) {
                return !in_array($item->final_service_type, ['TRANSFER', 'DEPARTURE']);
            });
            $data = array_values($filtered_data);
        endif;

        if($search['type'] == "departure"):
            $filtered_data = array_filter($data, function($item) {
                return !in_array($item->final_service_type, ['ARRIVAL']);
            });
            $data = array_values($filtered_data);
        endif;
        
        //RECORREMOS LOS ITEMS, AGREGANDO LOS ITEMS DE PAGO
        foreach($data as $key => $value):
            $value->payment_items = [];
            $ids[] = $value->reservation_id;
        endforeach;
                
        $ids = array_unique($ids);
        
        //BUSCAMOS LO PAGOS DE LA RESERVACIÓN
        if( !empty($ids) ){
        $payments = DB::select("SELECT 
                                    reservation_id, 
                                    payment_method, 
                                    total,
                                    currency, 
                                    object, 
                                    reference, 
                                    created_at
                                FROM payments 
                                WHERE reservation_id IN (" . implode(',', $ids) . ") 
                                    AND payment_method IN('CARD','STRIPE','PAYPAL') 
                                    AND deleted_at IS NULL");
        }
        
        //Listamos las reservaciones que tuvieron pago en Tarjeta o PayPal Pero que no tiene el objeto para parsear la información..
        if(sizeof($payments) >= 1):
            foreach($payments as $key => $value):
                if(in_array( $value->payment_method, ['CARD','STRIPE','PAYPAL'] )):                    
                    $object = json_decode($value->object);
                    if( !is_object($object) ):
                        $payments_with_errors[] = $value;
                    endif;
                    if( is_object($object) ):
                        $payments_with_success[] = $value;
                    endif;
                endif;
            endforeach;
        endif;

        if( sizeof($payments_with_success) ):
            foreach($payments_with_success as $keyP => $valueP):
                foreach($data as $keyD => $valueD):
                    if($valueP->reservation_id == $valueD->reservation_id):
                        $valueP->rezervation = $valueD;
                    endif;
                endforeach;
            endforeach;
        endif;

        if( sizeof($payments_with_errors) ):
            foreach($payments_with_errors as $keyP => $valueP):
                foreach($data as $keyD => $valueD):
                    if($valueP->reservation_id == $valueD->reservation_id):
                        $valueP->rezervation = $valueD;
                    endif;
                endforeach;
            endforeach;
        endif;

        $pdf = new Fpdf();
    
        if(sizeof($payments_with_success) >=1 ):
            foreach($payments_with_success as $key => $value):
                
                $info = [
                    "client_full_name" => "",
                    "payment_amount" => "",
                    "reservation_creation_date" => "",
                    "client_phone" => "",
                    "client_email" => "",
                    "rez_code" => "",
                    "service_type" => "",
                    "destination_hotel" => "",
                    "pickup_date" => "",
                    "payment_type" => "",
                    "card_holder_name" => "",
                    "card_expiration_date" => "",
                    "card_postal_code" => "-",
                    "card_number" => "",
                    "payment_invoice" => "",
                    "payment_email" => "",
                    "current_date" => date("M d, Y", strtotime( date("Y-m-d") ))
                ];

                $info['client_full_name'] =  ucfirst(ucwords(trim($value->rezervation->client_first_name))). " " . ucfirst(ucwords(trim($value->rezervation->client_last_name)));
                $info['reservation_creation_date'] = date("M/d, Y", strtotime($value->rezervation->created_at));
                $info['client_phone'] = $value->rezervation->client_phone;
                $info['client_email'] = $value->rezervation->client_email;
                $info['rez_code'] = $value->rezervation->code;
                
                $hotel = "";                
                if($value->rezervation->zone_one_is_primary == 1 && $value->rezervation->zone_two_is_primary == 0):
                    $service_type = (($value->rezervation->language == "en")?'Arrival':'Llegada');
                    $hotel = $value->rezervation->to_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 0 && $value->rezervation->zone_two_is_primary == 1):                    
                    $service_type = (($value->rezervation->language == "en")?'Departure':'Salida');
                    $hotel = $value->rezervation->to_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 0 && $value->rezervation->zone_two_is_primary == 0):                    
                    $service_type = (($value->rezervation->language == "en")?'Transfer One Way':'Traslado de Ida');
                    $hotel = $value->rezervation->from_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 0 && $value->rezervation->zone_two_is_primary == 0 && $value->rezervation->is_round_trip == 1):                    
                    $service_type = (($value->rezervation->language == "en")?'Transfer Round Trip':'Traslado Ida y Vuelta');
                    $hotel = $value->rezervation->from_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 1 && $value->rezervation->zone_two_is_primary == 0 && $value->rezervation->is_round_trip == 1):                    
                    $service_type = (($value->rezervation->language == "en")?'Round Trip':'Viaje Redondo');
                    $hotel = $value->rezervation->to_name;
                endif;

                $info['service_type'] = $service_type;
                $info['destination_hotel'] = $hotel;
                $info['pickup_date'] = date("M d, Y", strtotime($value->rezervation->op_one_pickup));

                if( $value->payment_method == "CARD" || $value->payment_method == "STRIPE" ):
                    $info = $this->Stripe($value, $info);
                endif;

                if( $value->payment_method == "PAYPAL" ):
                    $info = $this->PayPal($value, $info);
                endif;

                $pdf->AddPage('P', 'Letter');
                $pdf->SetFont('Arial','', 11);
                $pdfWidth = $pdf->GetPageWidth();
                $pdfHeight = $pdf->GetPageHeight();                
                $pdf->SetXY(0, 0);

                if($value->rezervation->language == "es"):
                    $pdf->Image('https://ik.imagekit.io/zqiqdytbq/bookings/CCForm/Spanish.jpg', 0,0, $pdfWidth, $pdfHeight);
                endif;

                if($value->rezervation->language == "en"):
                    $pdf->Image('https://ik.imagekit.io/zqiqdytbq/bookings/CCForm/English.jpg', 0,0, $pdfWidth, $pdfHeight);
                endif;
                
                $pdf->SetXY(60, 69);
                $pdf->Cell(1,1, utf8_decode($info['card_holder_name']), 0, 0, "C");

                $pdf->SetXY(90, 84);
                $pdf->Cell(1,1, $info['payment_amount'], 0, 0, "C");

                $pdf->SetXY(158, 84);
                $pdf->Cell(1,1, $info['reservation_creation_date'], 0, 0, "C");

                $pdf->SetXY(58, 98);
                $pdf->Cell(1,1, $info['client_phone'], 0, 0, "C");

                $pdf->SetXY(140, 98);
                $pdf->Cell(1,1, $info['client_email'], 0, 0, "C");

                $pdf->SetXY(90, 117);
                $pdf->Cell(1,1, $info['rez_code'], 0, 0, "C");

                $pdf->SetXY(171, 117);
                $pdf->Cell(1,1, $info['service_type'], 0, 0, "C");

                $pdf->SetXY(115, 124);
                $pdf->Cell(1,1, utf8_decode($info['destination_hotel']), 0, 0, "C");

                $pdf->SetXY(55, 130);
                $pdf->Cell(1,1, $info['pickup_date'], 0, 0, "C");

                if($info['payment_type'] == "VISA"):
                    $pdf->SetXY(27.2, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "MASTERCARD"):
                    $pdf->SetXY(42.6, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "DISCOVER"):
                    $pdf->SetXY(72, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "AMEX"):
                    $pdf->SetXY(96, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "PAYPAL"):
                    $pdf->SetXY(136, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                $pdf->SetXY(125, 155);
                $pdf->Cell(1,1, utf8_decode($info['card_holder_name']), 0, 0, "C");

                $pdf->SetXY(77, 165);
                $pdf->Cell(1,1, $info['card_expiration_date'], 0, 0, "C");

                $pdf->SetXY(130, 165);
                $pdf->Cell(1,1, $info['card_postal_code'], 0, 0, "C");

                $pdf->SetXY(130, 175);
                $pdf->Cell(1,1, $info['card_number'], 0, 0, "C");

                $pdf->SetXY(98, 184);
                $pdf->Cell(1,1, $info['payment_invoice'], 0, 0, "C");

                $pdf->SetXY(110, 194);
                $pdf->Cell(1,1, $info['payment_email'], 0, 0, "C");
                
                $pdf->SetXY(110, 238);
                $pdf->Cell(1,1, utf8_decode(( !empty($info['card_holder_name']) ? $info['card_holder_name'] : $info['client_full_name'] )), 0, 0, "C");

                $pdf->SetXY(55, 247);
                $pdf->Cell(1,1, $info['current_date'], 0, 0, "C");

            endforeach;
        endif;
        
        $pdf->AddPage('P', 'Letter');
        $pdf->SetFont('Arial','', 11);

        if(sizeof($payments_with_errors) >=1 ):
            foreach($payments_with_errors as $key => $value):
                
                $info = [
                    "client_full_name" => "",
                    "payment_amount" => "",
                    "reservation_creation_date" => "",
                    "client_phone" => "",
                    "client_email" => "",
                    "rez_code" => "",
                    "service_type" => "",
                    "destination_hotel" => "",
                    "pickup_date" => "",
                    "payment_type" => "",
                    "card_holder_name" => "",
                    "card_expiration_date" => "",
                    "card_postal_code" => "-",
                    "card_number" => "",
                    "payment_invoice" => "",
                    "payment_email" => "",
                    "current_date" => date("M d, Y", strtotime( date("Y-m-d") ))
                ];    

                $info['client_full_name'] =  ucfirst(ucwords(trim($value->rezervation->client_first_name))). " " . ucfirst(ucwords(trim($value->rezervation->client_last_name)));
                $info['reservation_creation_date'] = date("M/d, Y", strtotime($value->rezervation->created_at));
                $info['client_phone'] = $value->rezervation->client_phone;
                $info['client_email'] = $value->rezervation->client_email;
                $info['rez_code'] = $value->rezervation->code;
                
                $hotel = "";                
                if($value->rezervation->zone_one_is_primary == 1 && $value->rezervation->zone_two_is_primary == 0):
                    $service_type = (($value->rezervation->language == "en")?'Arrival':'Llegada');
                    $hotel = $value->rezervation->to_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 0 && $value->rezervation->zone_two_is_primary == 1):                    
                    $service_type = (($value->rezervation->language == "en")?'Departure':'Salida');
                    $hotel = $value->rezervation->to_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 0 && $value->rezervation->zone_two_is_primary == 0):                    
                    $service_type = (($value->rezervation->language == "en")?'Transfer One Way':'Traslado de Ida');
                    $hotel = $value->rezervation->from_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 0 && $value->rezervation->zone_two_is_primary == 0 && $value->rezervation->is_round_trip == 1):                    
                    $service_type = (($value->rezervation->language == "en")?'Transfer Round Trip':'Traslado Ida y Vuelta');
                    $hotel = $value->rezervation->from_name;
                endif;

                if($value->rezervation->zone_one_is_primary == 1 && $value->rezervation->zone_two_is_primary == 0 && $value->rezervation->is_round_trip == 1):                    
                    $service_type = (($value->rezervation->language == "en")?'Round Trip':'Viaje Redondo');
                    $hotel = $value->rezervation->to_name;
                endif;
                
                $info['service_type'] = $service_type;
                $info['destination_hotel'] = $hotel;
                $info['pickup_date'] = date("M d, Y", strtotime($value->rezervation->op_one_pickup));    
                $info['card_holder_name'] = $info['client_full_name'];
                $info['payment_amount'] = $value->total." ".$value->currency;
                
                $pdf->AddPage('P', 'Letter');
                $pdf->SetFont('Arial','', 11);
                $pdfWidth = $pdf->GetPageWidth();
                $pdfHeight = $pdf->GetPageHeight();                
                $pdf->SetXY(0, 0);

                if($value->rezervation->language == "es"):
                    $pdf->Image('https://ik.imagekit.io/zqiqdytbq/bookings/CCForm/Spanish.jpg', 0,0, $pdfWidth, $pdfHeight);
                endif;

                if($value->rezervation->language == "en"):
                    $pdf->Image('https://ik.imagekit.io/zqiqdytbq/bookings/CCForm/English.jpg', 0,0, $pdfWidth, $pdfHeight);
                endif;
                
                $pdf->SetXY(60, 69);
                $pdf->Cell(1,1, utf8_decode($info['client_full_name']), 0, 0, "C");

                $pdf->SetXY(90, 84);
                $pdf->Cell(1,1, $info['payment_amount'], 0, 0, "C");

                $pdf->SetXY(158, 84);
                $pdf->Cell(1,1, $info['reservation_creation_date'], 0, 0, "C");

                $pdf->SetXY(58, 98);
                $pdf->Cell(1,1, $info['client_phone'], 0, 0, "C");

                $pdf->SetXY(140, 98);
                $pdf->Cell(1,1, $info['client_email'], 0, 0, "C");

                $pdf->SetXY(90, 117);
                $pdf->Cell(1,1, $info['rez_code'], 0, 0, "C");

                $pdf->SetXY(171, 117);
                $pdf->Cell(1,1, $info['service_type'], 0, 0, "C");

                $pdf->SetXY(115, 124);
                $pdf->Cell(1,1, utf8_decode($info['destination_hotel']), 0, 0, "C");

                $pdf->SetXY(55, 130);
                $pdf->Cell(1,1, $info['pickup_date'], 0, 0, "C");                

                if($info['payment_type'] == "VISA"):
                    $pdf->SetXY(27.2, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "MASTERCARD"):
                    $pdf->SetXY(42.6, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "DISCOVER"):
                    $pdf->SetXY(72, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "AMEX"):
                    $pdf->SetXY(96, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                if($info['payment_type'] == "PAYPAL"):
                    $pdf->SetXY(136, 146.8);
                    $pdf->Cell(1,1, 'x', 0, 0, "C");
                endif;

                $pdf->SetXY(125, 155);
                $pdf->Cell(1,1, utf8_decode($info['card_holder_name']), 0, 0, "C");

                $pdf->SetXY(77, 165);
                $pdf->Cell(1,1, $info['card_expiration_date'], 0, 0, "C");

                $pdf->SetXY(130, 165);
                $pdf->Cell(1,1, $info['card_postal_code'], 0, 0, "C");

                $pdf->SetXY(130, 175);
                $pdf->Cell(1,1, $info['card_number'], 0, 0, "C");

                $pdf->SetXY(98, 184);
                $pdf->Cell(1,1, $info['payment_invoice'], 0, 0, "C");

                $pdf->SetXY(110, 194);
                $pdf->Cell(1,1, $info['payment_email'], 0, 0, "C");
                
                $pdf->SetXY(110, 238);
                $pdf->Cell(1,1, utf8_decode($info['client_full_name']), 0, 0, "C");

                $pdf->SetXY(55, 247);
                $pdf->Cell(1,1, $info['current_date'], 0, 0, "C");

            endforeach;
        endif;

        $pdf->Output();
        exit;
    }

    public function Stripe($value, $info){

        $value->object = json_decode($value->object, true);
        if ($value->object === null && json_last_error() !== JSON_ERROR_NONE) {            
            return false;
        }

        $card_type = "";
        if(isset($value->object['payment_method_details']['card']) && $value->object['payment_method_details']['card']['brand'] == "mastercard"):
            $card_type = "MASTERCARD";
        endif;
        if(isset($value->object['payment_method_details']['card']) && $value->object['payment_method_details']['card']['brand'] == "visa"):
            $card_type = "VISA";
        endif;
        if(isset($value->object['payment_method_details']['card']) && $value->object['payment_method_details']['card']['brand'] == "amex"):
            $card_type = "AMEX";
        endif;
        if(isset($value->object['payment_method_details']['type']) && $value->object['payment_method_details']['type'] == "link"):
            $card_type = "LINK DE PAGO";
        endif;        

        
        $info['payment_type'] = "CARD";
        $info['payment_amount'] = number_format(round($value->object['amount'] / 100), 2)." ".$value->currency;
        $full_name = $value->rezervation->client_first_name." ".$value->rezervation->client_last_name;
        $info['card_holder_name'] = ucfirst(ucwords(trim(( isset($value->object['billing_details']['name']) ? $value->object['billing_details']['name'] : $full_name ))));
        if( isset($value->object['payment_method_details']['card']) ){
            $info['card_number'] = $value->object['payment_method_details']['card']['last4'];
            $info['card_expiration_date'] = $value->object['payment_method_details']['card']['exp_month']."/".$value->object['payment_method_details']['card']['exp_year'];
        }        
        $info['payment_email'] = $value->object['billing_details']['email'];
        $info['payment_invoice'] = $value->object['id'];
        $info['card_postal_code'] = $value->object['billing_details']['address']['postal_code'];
        $info['payment_type'] = $card_type;

        return $info;
    }

    public function PayPal($value, $info)
    {
        $value->object = json_decode($value->object, true);
        if ($value->object === null && json_last_error() !== JSON_ERROR_NONE) {            
            return false;
        }

        $info['payment_type'] = "PAYPAL";
        $info['payment_amount'] = number_format(round($value->object['mc_gross']), 2)." ".$value->currency;
        $info['card_holder_name'] = ucfirst(ucwords(trim(( isset($value->object['first_name']) ? $value->object['first_name'] : $value->rezervation->client_first_name )))). " " . ucfirst(ucwords(trim(( isset($value->object['last_name']) ? $value->object['last_name'] : $value->rezervation->client_last_name ))));
        $info['payment_email'] = $value->object['payer_email'];
        $info['payment_invoice'] = $value->object['txn_id'];        
        
        return $info;
    }

    public function QueryCCForm($search)
    {
        return DB::select("SELECT 
                                rez.id as reservation_id, 
                                rez.*, 
                                it.*, 
                                serv.name as service_name, 
                                it.op_one_pickup as filtered_date, 
                                'arrival' as operation_type, 
                                sit.name as site_name, '' as messages,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                CASE
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                    ELSE 'CONFIRMADO'
                                END AS status,
                                zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                                zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                                CASE 
                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type
                            FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
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
                            WHERE it.op_one_pickup BETWEEN :init_date_one AND :init_date_two
                                AND rez.is_cancelled = 0
                            GROUP BY 
                                    it.id, 
                                    rez.id, 
                                    rez.uuid, 
                                    rez.categories, 
                                    rez.client_first_name, 
                                    rez.client_last_name, 
                                    rez.client_email, 
                                    rez.client_email, 
                                    rez.client_phone, 
                                    rez.currency, 
                                    rez.language, 
                                    rez.rate_group, 
                                    rez.is_cancelled, 
                                    rez.is_commissionable, 
                                    rez.is_advanced, 
                                    rez.pay_at_arrival, 
                                    rez.site_id, 
                                    rez.destination_id, 
                                    rez.created_at, 
                                    rez.updated_at, 
                                    rez.reference, 
                                    rez.call_center_agent_id, 
                                    rez.agent_id_after_sales, 
                                    rez.agent_id_pull_sales, 
                                    rez.affiliate_id, 
                                    rez.vendor_id, 
                                    rez.payment_reconciled, 
                                    rez.user_id, 
                                    rez.accept_messages, 
                                    rez.terminal, 
                                    rez.type_after_sales, 
                                    rez.cancellation_type_id, 
                                    rez.comments, 
                                    rez.is_duplicated, 
                                    rez.open_credit, 
                                    rez.is_complete, 
                                    rez.origin_sale_id, 
                                    rez.reference_two, 
                                    rez.is_quotation, 
                                    rez.is_last_minute, 
                                    rez.was_is_quotation, 
                                    rez.expires_at, 
                                    rez.campaign, 
                                    rez.reserve_rating, 

                                    it.reservation_id,
                                    it.code,
                                    it.client_first_name2,
                                    it.client_last_name2,
                                    it.destination_service_id,
                                    it.from_name,
                                    it.from_lat,
                                    it.from_lng,
                                    it.from_zone,
                                    it.to_name,
                                    it.to_lat,
                                    it.to_lng,
                                    it.to_zone,
                                    it.distance_time,
                                    it.distance_km,
                                    it.is_round_trip,
                                    it.flight_number,
                                    it.flight_data,
                                    it.passengers,

                                    zone_one.id,
                                    zone_one.name,
                                    zone_one.is_primary,
                                    zone_one.cut_off_operation,
                                    it.vehicle_id_one,
                                    it.driver_id_one,
                                    it.op_one_status,
                                    it.op_one_status_operation,
                                    it.op_one_time_operation,
                                    it.op_one_comments,
                                    it.op_one_preassignment,
                                    it.op_one_operating_cost,
                                    it.op_one_pickup,
                                    it.op_one_confirmation,
                                    it.op_one_message,
                                    it.op_one_cancellation_type_id,
                                    it.op_one_cancelled_at,
                                    it.op_one_cancellation_level,

                                    zone_two.id,
                                    zone_two.name,
                                    zone_two.is_primary,
                                    zone_two.cut_off_operation,
                                    it.vehicle_id_two,                            
                                    it.driver_id_two,
                                    it.op_two_status,
                                    it.op_two_status_operation,
                                    it.op_two_time_operation,
                                    it.op_two_comments,
                                    it.op_two_preassignment,
                                    it.op_two_operating_cost,
                                    it.op_two_pickup,
                                    it.op_two_confirmation,
                                    it.op_two_message,
                                    it.op_two_cancellation_type_id,
                                    it.op_two_cancelled_at,
                                    it.op_two_cancellation_level,

                                    it.created_at,
                                    it.updated_at,
                                    it.spam,
                                    it.spam_count,
                                    it.op_one_operation_close,
                                    it.op_two_operation_close,
                                    it.type_service,
                                    it.is_open,
                                    it.open_service_time,
                                    serv.id,
                                    serv.name,
                                    sit.id,
                                    sit.name
                            
                            UNION 
                SELECT rez.id as reservation_id, rez.*, it.*, serv.name as service_name, it.op_two_pickup as filtered_date, 'departure' as operation_type, sit.name as site_name, '' as messages,
                        COALESCE(SUM(s.total_sales), 0) as total_sales, COALESCE(SUM(p.total_payments), 0) as total_payments,
                        CASE
                                WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                ELSE 'CONFIRMADO'
                        END AS status,
                        zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                        zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                        CASE                                                     
                            WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1  THEN 'DEPARTURE'
                            WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                        END AS final_service_type
                FROM reservations_items as it
                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                INNER JOIN sites as sit ON sit.id = rez.site_id
                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
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
                WHERE it.op_two_pickup BETWEEN :init_date_three AND :init_date_four
                    AND rez.is_cancelled = 0
                GROUP BY 
                        it.id, 
                        rez.id, 
                        rez.uuid, 
                        rez.categories, 
                        rez.client_first_name, 
                        rez.client_last_name, 
                        rez.client_email, 
                        rez.client_email, 
                        rez.client_phone, 
                        rez.currency, 
                        rez.language, 
                        rez.rate_group, 
                        rez.is_cancelled, 
                        rez.is_commissionable, 
                        rez.is_advanced, 
                        rez.pay_at_arrival, 
                        rez.site_id, 
                        rez.destination_id, 
                        rez.created_at, 
                        rez.updated_at, 
                        rez.reference, 
                        rez.call_center_agent_id, 
                        rez.agent_id_after_sales, 
                        rez.agent_id_pull_sales, 
                        rez.affiliate_id, 
                        rez.vendor_id, 
                        rez.payment_reconciled, 
                        rez.user_id, 
                        rez.accept_messages, 
                        rez.terminal, 
                        rez.type_after_sales, 
                        rez.cancellation_type_id, 
                        rez.comments, 
                        rez.is_duplicated, 
                        rez.open_credit, 
                        rez.is_complete, 
                        rez.origin_sale_id, 
                        rez.reference_two, 
                        rez.is_quotation, 
                        rez.is_last_minute, 
                        rez.was_is_quotation, 
                        rez.expires_at, 
                        rez.campaign, 
                        rez.reserve_rating, 

                        it.reservation_id,
                        it.code,
                        it.client_first_name2,
                        it.client_last_name2,
                        it.destination_service_id,
                        it.from_name,
                        it.from_lat,
                        it.from_lng,
                        it.from_zone,
                        it.to_name,
                        it.to_lat,
                        it.to_lng,
                        it.to_zone,
                        it.distance_time,
                        it.distance_km,
                        it.is_round_trip,
                        it.flight_number,
                        it.flight_data,
                        it.passengers,

                        zone_one.id,
                        zone_one.name,
                        zone_one.is_primary,
                        zone_one.cut_off_operation,
                        it.vehicle_id_one,
                        it.driver_id_one,
                        it.op_one_status,
                        it.op_one_status_operation,
                        it.op_one_time_operation,
                        it.op_one_comments,
                        it.op_one_preassignment,
                        it.op_one_operating_cost,
                        it.op_one_pickup,
                        it.op_one_confirmation,
                        it.op_one_message,
                        it.op_one_cancellation_type_id,
                        it.op_one_cancelled_at,
                        it.op_one_cancellation_level,

                        zone_two.id,
                        zone_two.name,
                        zone_two.is_primary,
                        zone_two.cut_off_operation,
                        it.vehicle_id_two,                            
                        it.driver_id_two,
                        it.op_two_status,
                        it.op_two_status_operation,
                        it.op_two_time_operation,
                        it.op_two_comments,
                        it.op_two_preassignment,
                        it.op_two_operating_cost,
                        it.op_two_pickup,
                        it.op_two_confirmation,
                        it.op_two_message,
                        it.op_two_cancellation_type_id,
                        it.op_two_cancelled_at,
                        it.op_two_cancellation_level,

                        it.created_at,
                        it.updated_at,
                        it.spam,
                        it.spam_count,
                        it.op_one_operation_close,
                        it.op_two_operation_close,
                        it.type_service,
                        it.is_open,
                        it.open_service_time,
                        serv.id,
                        serv.name,
                        sit.id,
                        sit.name",
                [
                        "init_date_one" => $search['init_date'],
                        "init_date_two" => $search['end_date'],
                        "init_date_three" => $search['init_date'],
                        "init_date_four" => $search['end_date'],
                ]);
    }

    public function QueryCCFormId($id)
    {
        return DB::select("SELECT 
                                rez.id as reservation_id, 
                                rez.*, 
                                it.*, 
                                serv.name as service_name, 
                                it.op_one_pickup as filtered_date, 
                                'arrival' as operation_type, 
                                sit.name as site_name, 
                                '' as messages,
                                COALESCE(SUM(s.total_sales), 0) as total_sales, 
                                COALESCE(SUM(p.total_payments), 0) as total_payments,
                                CASE
                                    WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                    ELSE 'CONFIRMADO'
                                END AS status,
                                zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                                zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                                CASE 
                                    WHEN zone_one.is_primary = 1 THEN 'ARRIVAL'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 1 THEN 'DEPARTURE'
                                    WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                                END AS final_service_type
                            FROM reservations_items as it
                                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                                INNER JOIN sites as sit ON sit.id = rez.site_id
                                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
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
                            WHERE rez.id = :codeOne
                                AND rez.is_cancelled = 0
                            GROUP BY it.id,
                                    rez.uuid, 
                                    rez.id, 
                                    serv.id, 
                                    sit.id, 
                                    zone_one.id, 
                                    zone_two.id
                            
                            UNION 
                SELECT 
                        rez.id as reservation_id, 
                        rez.*, 
                        it.*, 
                        serv.name as service_name, 
                        it.op_two_pickup as filtered_date, 
                        'departure' as operation_type, 
                        sit.name as site_name, 
                        '' as messages,
                        COALESCE(SUM(s.total_sales), 0) as total_sales, 
                        COALESCE(SUM(p.total_payments), 0) as total_payments,
                        CASE
                                WHEN COALESCE(SUM(s.total_sales), 0) - COALESCE(SUM(p.total_payments), 0) > 0 THEN 'PENDIENTE'
                                ELSE 'CONFIRMADO'
                        END AS status,
                        zone_one.id as zone_one_id, zone_one.name as zone_one_name, zone_one.is_primary as zone_one_is_primary,
                        zone_two.id as zone_two_id, zone_two.name as zone_two_name, zone_two.is_primary as zone_two_is_primary,
                        CASE                                                     
                            WHEN zone_two.is_primary = 0 AND zone_one.is_primary = 1  THEN 'DEPARTURE'
                            WHEN zone_one.is_primary = 0 AND zone_two.is_primary = 0 THEN 'TRANSFER'
                        END AS final_service_type
                FROM reservations_items as it
                INNER JOIN reservations as rez ON rez.id = it.reservation_id
                INNER JOIN destination_services as serv ON serv.id = it.destination_service_id
                INNER JOIN sites as sit ON sit.id = rez.site_id
                INNER JOIN zones as zone_one ON zone_one.id = it.from_zone
                INNER JOIN zones as zone_two ON zone_two.id = it.to_zone
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
                WHERE rez.id = :codeTwo
                AND rez.is_cancelled = 0
                GROUP BY it.id, 
                        rez.uuid, 
                        rez.id, 
                        serv.id, 
                        sit.id, 
                        zone_one.id, 
                        zone_two.id",
                [
                    "codeOne" => $id,
                    "codeTwo" => $id,
                ]);
    } 
}