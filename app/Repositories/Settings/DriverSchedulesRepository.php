<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

//MODELS
use App\Models\DriverSchedule;

class DriverSchedulesRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        $schedules = DriverSchedule::orderBy('date', 'ASC')
                                    ->orderBy('check_in_time', 'ASC')
                                    ->get();
        return view('settings.schedules.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Horarios de conductores",
                    "active" => true
                ]
            ],
            'schedules' => $schedules,
            'data' => [],
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