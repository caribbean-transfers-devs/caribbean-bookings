<?php

namespace App\Traits;
use Illuminate\Http\Response;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\User;
use App\Models\OriginSale;
use App\Models\Enterprise;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Zones;
use App\Models\DestinationService;
use App\Models\CancellationTypes;
use App\Models\ExchangeRateReport;
use App\Models\ExchangeRateCommission;
use App\Models\SalesType;
use App\Models\ContactPoints;

trait FiltersTrait
{

    //NOS TRAE EL TIPO DE CAMBIO DEPENDIENDO DE LA FECHA, PARA LOS REPORTES
    public function Exchange($in, $end)
    {
        $in = ( isset($in) ? $in : date('Y-m-d') );
        $end = ( isset($in) ? $in : date('Y-m-d') );
        $report = ExchangeRateReport::where('status', 1)->where('date_init', '<=', $in)
                                ->where('date_end', '>=', $end)
                                ->first();

        if ($report) {
            return $report->exchange; // Ejemplo: 25.50
        } else {
            return 18;
        }
    }

    public function ExchangeCommission($in, $end)
    {
        $in = ( isset($in) ? $in : date('Y-m-d') );
        $end = ( isset($in) ? $in : date('Y-m-d') );
        $report = ExchangeRateCommission::where('status', 1)->where('date_init', '<=', $in)
                                ->where('date_end', '>=', $end)
                                ->first();

        if ($report) {
            return $report->exchange; // Ejemplo: 25.50
        } else {
            return 16.5;
        }
    }

    public function PercentageCommissionInvestment()
    {
        return 20; // Ejemplo: 20.00
    }

    public function Users()
    {
        return User::where('status', 1)->get();
    }

    //NOS TRAE LOS AGENTES DE CALL CENTER
    public function CallCenterAgent(array $status = [])
    {
        $status = array_filter($status, fn($id) => is_numeric($id) && ctype_digit((string) $id));

        // Base de la consulta
        $query = User::where('is_commission', 1)->with('target');

        // Si hay usuarios filtrados, aplicar whereIn
        if (!empty($status)) {
            $query->whereIn('status', $status);
        }

        return $query->get();        
    }

    public function Enterprises()
    {
        return Enterprise::all();
    }

    //TIPO DE SERVICIO
    public function Services()
    {
        return array(
            "0" => "One Way",
            "1" => "Round Trip",
        );
    }

    //SITIOS O AGENCIAS
    public function Sites()
    {
        return DB::select("SELECT id, name, type_site FROM sites ORDER BY name ASC");
    }

    public function Origins()
    {
        return OriginSale::select('id', 'code')->get()->prepend((object)[
            "id" => 0,
            "code" => "PAGINA WEB"
        ]);
    }

    //ESTATUS DE RESERVACIÓN
    public function reservationStatus()
    {
        return array(
            "CONFIRMED" => "Confirmado",
            "PAY_AT_ARRIVAL" => "Pago a la llegada",
            "PENDING" => "Pendiente",
            "CREDIT" => "Crédito",
            "OPENCREDIT" => "Credito abierto",
            "CANCELLED" => "Cancelado",
            "DUPLICATED" => "Duplicado",
            "QUOTATION" => "Cotización",
        );
    }

    //TIPO DE SERVICIO EN OPERACIÓN
    public function servicesOperation()
    {
        return array(
            "ARRIVAL" => "Llegada",
            "DEPARTURE" => "Salida",
            "TRANSFER" => "Transalado",
        );
    }

    //TIPO DE VEHÍCULO
    public function Vehicles()
    {
        return DestinationService::get();
    }

    //ZONAS DE ORIGEN Y DESTINO
    /**
     * Se evita llamar Zones::all() cuando hay un filtro.
     * Se usa query() para construir dinámicamente la consulta.
     */
    public function Zones($destination_id = NULL)
    {
        $query = Zones::query();

        if ($destination_id) {
            $query->where('destination_id', $destination_id);
        }
    
        return $query->get();
    }

    //ESTATUS DE SERVICIO
    public function statusOperationService()
    {
        return array(
            "PENDING" => "Pendiente",
            "COMPLETED" => "Completado",
            "NOSHOW" => "No se presentó",
            "CANCELLED" => "Cancelado",
        );
    }

    //SON LA UNIDADES QUE SE ASIGNAN EN LA OPERACIÓN, PERO QUE SON CONSIDERADOS COMO LOS VEHICULOS QUE TENEMOS
    // SI LE MANDAMOS EL PARAMERO ACTION COMO FILTERS, NOS TRAE TODAS LAS UNIDADES SI IMPORTAR QUE ESTEN INACTIVAS
    // SI LE MANDAMOS EL PARAMERO ACTION COMO DIFERENTE DE FILTERS, NOS TRAE TODAS LAS UNIDADES QUE SOLO ESTEN ACTIVAS
    /**
     * Se optimizó la consulta con query() en lugar de if separado.
     * Se asegura de usar Eager Loading para relaciones.
     */
    public function Units($action = "filters")
    {
        $query = Vehicle::with(['enterprise', 'destination_service', 'destination']);

        if ($action !== "filters") {
            $query->where('status', 1);
        }
    
        return $query->get();            
    }

    //CONDUCTOR
    /**
     * Se usa query() para construir la consulta más eficiente.
     */
    public function Drivers($action = "filters")
    {
        $query = Driver::orderBy('names', 'ASC');

        if ($action !== "filters") {
            $query->where('status', 1);
        }
    
        return $query->get();
    }

    //ESTATUS DE OPERACIÓN
    public function statusOperation()
    {
        return array(
            "PENDING" => "Pendiente",
            "E" => "E",
            "C" => "C",
            "OK" => "OK",
        );
    }    
    
    //ESTATUS DE PAGO DE RESERVACIÓN
    public function paymentStatus()
    {
        return array(
            "PAID" => "Pagado",
            "CREDIT" => "Crédito",
            "PENDING" => "Pendiente",
        );
    }

    //MONEDA DE RESERVACIÓN
    public function Currencies()
    {
        return array(
            "USD" => "USD",
            "MXN" => "MXN",
        );
    }
    
    //METODO DE PAGO DE RESERVACIÓN
    public function Methods()
    {
        return array(
            "CREDIT" => "CREDITO",
            "CASH" => "EFECTIVO",
            "SANTANDER" => "SANTANDER",
            "STRIPE" => "STRIPE",
            "PAYPAL" => "PAYPAL",
            "MIFEL" => "MIFEL",
        );
    }

    //MOTIVOS DE CANCELACIÓN
    public function CancellationTypes()
    {
        return CancellationTypes::where('status',1)->get();
    }

    public function TypeSales()
    {
        return SalesType::all();
    }

    /**
     * Se evita llamar where() si $destination_id es NULL.
     * Se usa query() para construir dinámicamente la consulta.
     */
    public function ContactPoints($destination_id = NULL){
        $query = ContactPoints::query();

        if ($destination_id) {
            $query->where('destination_id', $destination_id);
        }
    
        return $query->get();
    }

    public function parseArrayQuery($data, $marks = NULL){
        if (is_array($data)) {
            $filteredData = array_filter($data, function($value) {
                return $value !== NULL && $value !== 0;
            });
            
            // Envuelve cada valor del array en comillas simples
            $string = implode(',', array_map(function($value) use ($marks) {
                if( $marks != NULL && $marks == "single" ){
                    return "'" . $value . "'";
                }else if( $marks != NULL && $marks == "single" ){
                    return '"' . $value . '"';
                }else{
                    return $value;   
                }
            }, $filteredData));
            return $string;
        } else {
            return "'" . $data . "'";
        }
    }
}