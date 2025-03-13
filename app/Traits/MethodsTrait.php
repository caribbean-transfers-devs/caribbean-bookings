<?php

namespace App\Traits;
use Illuminate\Http\Response;

//PACKAGES
use Carbon\Carbon;

//MODELS
use App\Models\User;
trait MethodsTrait
{

    public static function parseArray($user):array
    {
        if (is_array($user)) {
            return array_filter($user, fn($value) => $value !== null); // Evita eliminar valores como 0 o "0"
        }
    
        if (!is_string($user)) {
            return []; // Si no es string ni array, devuelve un array vacío
        }
    
        preg_match_all('/["\']([^"\']+)["\']/', $user, $matches);
        return $matches[1] ?? []; // Devuelve un array vacío si no hay coincidencias        
    }

    /**
     * Función para obtener la información de usuario o usuarios
     */
    public static function DataUser(array $users = [], array $status = []):object
    {
        // Filtrar solo valores numéricos para evitar errores en la consulta
        $users = array_filter($users, fn($id) => is_numeric($id) && ctype_digit((string) $id));
        //$status = array_filter($status, fn($id) => is_numeric($status) && ctype_digit((string) $id));

        // Construcción de la consulta base
        $query = User::where('is_commission', 1)->with('target');

        // Si hay usuarios filtrados, aplicar whereIn
        if (!empty($users)) {
            $query->whereIn('id', $users);
        }

        $query->whereIn('status', $status ?: [1]);

        return $query->get();
    }

    /**
     * Función para dividir el rango de fechas en 'init' y 'end'.
     * @param date: es la fecha en el siguiente formato ejemplo: 2025-01-01 - 2025-01-31
     */
    public static function parseDateRange($date)
    {
        $dates = isset($date) && !empty($date)
        ? (strpos($date, ' - ') !== false 
            ? explode(" - ", $date) 
            : (strpos($date, ' a ') !== false 
                ? explode(" a ", $date) 
                : [$date, $date])) 
        : [date('Y-m-d'), date('Y-m-d')];

        // Si solo hay una fecha, duplicarla para evitar errores
        if (count($dates) === 1) {
            $dates[1] = $dates[0];
        }

        return [
            'init' => trim($dates[0]), // Eliminamos espacios en blanco extra
            'end' => trim($dates[1]),
        ];
    }

    /**
     * Función para dividir el rango de fechas en 'init' y 'end', de un mes.
     */
    public static function parseDateRangeMonth($date)
    {
        $startDate = Carbon::parse($date); // Tomar la primera fecha
        $start = $startDate->copy()->startOfMonth();
        $end = $startDate->copy()->endOfMonth();
        return [
            'init' => $start->toDateString(),
            'end' => $end->toDateString(),
            'initTime' => $start->toDateTimeString(),
            'endTime' => $end->toDateTimeString(),
            'initCarbon' => $start,
            'endCarbon' => $end,
        ];
    }    

    /**
     * Función para generar condiciones con FIND_IN_SET
     */
    public static function buildFindInSetQuery($values, $column, &$queryData)
    {
        $params = [];
        foreach ((array) $values as $key => $value) {
            $queryData["{$column}{$key}"] = $value;
            $params[] = "FIND_IN_SET(:{$column}{$key}, {$column}) > 0";
        }
        return " AND (" . implode(' OR ', $params) . ") ";
    }

    /**
     * Función para formatear arreglos en consultas SQL.
     */
    public static function parseArrayQuery2($data, $marks = null)
    {
        if (is_array($data)) {
            // $filteredData = array_filter($data, fn($value) => $value !== null && $value !== 0);
            $filteredData = array_filter($data, fn($value) => $value !== null); // Mantiene valores 0
            return implode(',', array_map(fn($value) => match ($marks) {
                'single' => "'" . addslashes($value) . "'",
                'double' => '"' . addslashes($value) . '"',
                default => $value,
            }, $filteredData));            
        }

        return isset($data) ? "'" . addslashes($data) . "'" : "NULL";
    }

    //GENERA EL ARREGLO PARA PODER GUARDAR LA VENTAS REALIZADAS POR UNO O VARIOS AGENTES DE CALL CENTER
    public static function SalesArrayStructure($start_d, $end_d, string $action = NULL, array $data = []):array
    {
        $bookings_month = [];
        $date = clone $start_d; // Clonar la fecha antes de modificarla
    
        while ($date->lte($end_d)) {
            $bookings_month[$date->toDateString()] = [
                "DATE" => $date->format('j M'),
                "TOTAL" => 0,
                "USD" => 0,
                "MXN" => 0,
                "QUANTITY" => 0,
                "BOOKINGS" => [],
            ];
            if( $action != NULL ){
                $bookings_month[$date->toDateString()]["DATA"] = [];
                if( $data ){
                    foreach ($data as $value) {
                        if (!isset($bookings_month[$date->toDateString()]["DATA"][$value['id']])) {
                            $bookings_month[$date->toDateString()]["DATA"][$value['id']] = [
                                "NAME" => $value['name'],
                                "TOTAL" => 0,
                                "USD" => 0,
                                "MXN" => 0,
                                "QUANTITY" => 0,
                                "BOOKINGS" => []
                            ];
                        }                        
                    }
                }
            }            
            $date->addDay(); // Modificamos solo la copia, no la original
        }

        return $bookings_month;
    }

    public static function UserArrayStructure(array $data = []):array
    {
        $dataUser = [];

        if( $data ){
            foreach ($data as $value) {
                if (!isset($dataUser[$value['id']])) {
                    $dataUser[$value['id']] = [
                        "NAME" => $value['name'],
                        "TOTAL" => 0,
                        "USD" => 0,
                        "MXN" => 0,
                        "QUANTITY" => 0,
                        "BOOKINGS" => [],
                        "SETTINGS" => [
                            'daily_goal' => $value['daily_goal'] ?? 0,
                            'type_commission' => $value['type_commission'] ?? "target",
                            'percentage' => $value['percentage'] ?? 0,
                            'targets' => $value['target']['object'] ?? [],                            
                        ]
                    ];
                }                        
            }
        }
        
        return $dataUser;
    }

    public static function calculateTotalDiscount(float $amount = 0, float $percentage = 0):float
    {
        return $amount - ($amount * ($percentage / 100));
    }    
}