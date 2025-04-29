<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//TRAIT
use App\Traits\MethodsTrait;
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

//MODELS
use App\Models\DriverSchedule;

class DriverSchedulesRepository
{
    use MethodsTrait, FiltersTrait, QueryTrait;

    // CREATE INDEX date ON driver_schedules (date);
    // CREATE INDEX check_in_time ON driver_schedules (check_in_time);
    // CREATE INDEX check_out_time ON driver_schedules (check_out_time);
    // CREATE INDEX end_check_out_time ON driver_schedules (end_check_out_time);
    // CREATE INDEX extra_hours ON driver_schedules (extra_hours);
    // CREATE INDEX vehicle_id ON driver_schedules (vehicle_id);
    // CREATE INDEX driver_id ON driver_schedules (driver_id);
    // CREATE INDEX status ON driver_schedules (status);
    // CREATE INDEX status_unit ON driver_schedules (status_unit);
    // CREATE INDEX check_in_time_fleetio ON driver_schedules (check_in_time_fleetio);
    // CREATE INDEX check_out_time_fleetio ON driver_schedules (check_out_time_fleetio);
    // CREATE INDEX is_open ON driver_schedules (is_open);

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        set_time_limit(120); // Aumenta el límite a 60 segundos

        // Función auxiliar para obtener fechas seguras
        $dates = MethodsTrait::parseDateRange($request->date ?? '');

        $data = [
            "init" => $dates['init'],
            "end" => $dates['end'],
        ];

        $schedules = DriverSchedule::with(['vehicle.destination_service', 'vehicle.enterprise', 'driver'])->whereBetween('date', [$dates['init'] . " 00:00:00", $dates['end'] . " 23:59:59"])
                                    ->orderBy('date', 'ASC')
                                    ->orderBy('check_in_time', 'ASC')
                                    ->get();

        // dd($schedules);

        return view('settings.schedules.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Horarios de conductores",
                    "active" => true
                ]
            ],
            'schedules' => $schedules,
            'data' => $data,
        ]);
    }

    public function create($request){
        try {
            return view('settings.schedules.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('schedules.index'),
                        "name" => "Listado de horarios de conductores",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo horario",
                        "active" => true
                    ]
                ],                
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request){
        try {
            DB::beginTransaction();

            $schedule = new DriverSchedule();
            $schedule->date = $request->date;
            $schedule->check_in_time = $request->check_in_time;
            $schedule->check_out_time = $request->check_out_time;
            $schedule->vehicle_id = $request->vehicle_id ?? NULL;
            $schedule->driver_id = $request->driver_id ?? NULL;
            $schedule->save();

            DB::commit();

            return redirect()->route('schedules.index')->with('success', 'Tipo de cancelación creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('schedules.create')->with('danger', $e->getMessage());
        }
    }

    public function edit($request, $schedule){
        try {
            return view('settings.schedules.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('schedules.index'),
                        "name" => "Listado de horarios de conductores",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar el horario del conductor: ".$schedule->driver->names,
                        "active" => true
                    ]
                ],
                'schedule' => $schedule,
            ]);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function update($request, $schedule){
        try {
            DB::beginTransaction();

            $schedule->date = $request->date;
            $schedule->check_in_time = $request->check_in_time;
            $schedule->check_out_time = $request->check_out_time;

            if (!empty($request->end_check_out_time)) { 
                $time_in = Carbon::createFromFormat('H:i:s', $request->check_out_time.':00');
                $time_out = Carbon::createFromFormat('H:i:s', $request->end_check_out_time.':00');
                $difference = $time_in->diff($time_out);
                $schedule->end_check_out_time = $request->end_check_out_time;
                // Asigna el valor si hay diferencia, de lo contrario deja null
                if ($difference->h != 0 || $difference->i != 0) {
                    $schedule->extra_hours = sprintf('%02d:%02d:00', $difference->h, $difference->i);
                } else {
                    $schedule->extra_hours = null;
                }
            }
        
            $schedule->vehicle_id = ($request->vehicle_id ?? 0) != 0 ? $request->vehicle_id : NULL;
            $schedule->driver_id = ($request->driver_id ?? 0) != 0 ? $request->driver_id : NULL;
            $schedule->status = $request->status ?? NULL;
            $schedule->status_unit = $request->status_unit ?? NULL;
            $schedule->observations = $request->observations ?? NULL;
            $schedule->is_open = $request->is_open;
            $schedule->save();

            DB::commit();

            return redirect()->route('schedules.index')->with('success', 'Tipo de cancelación actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('schedules.edit', $schedule->id)->with('danger', $e->getMessage());
        }
    }

    public function destroy($request, $schedule){
        try {
            $schedule->delete();
            return redirect()->route('schedules.index')->with('success', 'Se elimimo correctamente el tipo de cancelación.');
        } catch (Exception $e) {
        }
    }    
}