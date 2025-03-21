<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
        $schedules = DriverSchedule::all();
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
            $schedule->vehicle_id = $request->vehicle_id;
            $schedule->driver_id = $request->driver_id;
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
            $cancellation = TypesCancellation::find($id);
            return view('settings.scheduless.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('schedules.index'),
                        "name" => "Listado de tipos de cancelaciones",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar el tipo de cancelación",
                        "active" => true
                    ]
                ],
                'cancellation' => $cancellation,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $schedule){
        try {
            DB::beginTransaction();

            $schedule->date = $request->date;
            $schedule->check_in_time = $request->check_in_time;
            $schedule->check_out_time = $request->check_out_time;
            $schedule->vehicle_id = $request->vehicle_id;
            $schedule->driver_id = $request->driver_id;
            $schedule->save();          

            DB::commit();

            return redirect()->route('schedules.index')->with('success', 'Tipo de cancelación actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('schedules.edit', $id)->with('danger', 'Error al actualizar el tipo de cancelación.');
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