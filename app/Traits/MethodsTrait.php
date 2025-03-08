<?php

namespace App\Traits;
use Illuminate\Http\Response;
trait MethodsTrait
{

    /**
     * Función para dividir el rango de fechas en 'init' y 'end'.
     */
    public static function parseDateRange($date)
    {
        $dates = explode(" - ", $date);
        return [
            'init' => $dates[0] ?? date("Y-m-d"),
            'end' => $dates[1] ?? date("Y-m-d"),
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
    public static function parseArrayQuery($data, $marks = null)
    {
        if (is_array($data)) {
            $filteredData = array_filter($data, fn($value) => $value !== null && $value !== 0);
            
            return implode(',', array_map(function($value) use ($marks) {
                return match ($marks) {
                    'single' => "'" . addslashes($value) . "'",
                    'double' => '"' . addslashes($value) . '"',
                    default => $value,
                };
            }, $filteredData));
        }
        return "'" . addslashes($data) . "'";
    }    

}