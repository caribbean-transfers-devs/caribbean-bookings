<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Enterprise;
use App\Models\Driver;

//TRAIT
use App\Traits\FiltersTrait;

//FACADES
use Illuminate\Support\Facades\DB;

class DriverRepository
{
    use FiltersTrait;

    public function index($request)
    {
        try {           
            $drivers = Driver::with('enterprise','destination')->get();
            return view('settings.drivers.index', [
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de conductores",
                        "active" => true
                    ]
                ],
                'drivers' => $drivers,
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            return view('settings.drivers.new', [
                'breadcrumbs' => [
                    [
                        "route" => route("drivers.index"),
                        "name" => "Listado de conductores",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo conductor",
                        "active" => true
                    ]                    
                ],
                'enterprises' => Enterprise::whereIn('type_enterprise', ['PROVIDER', 'MAIN'])->get(),
                'units' => $this->Units(),
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request){
        try {
            DB::beginTransaction();

            $driver = new Driver();
            $driver ->enterprise_id = $request->enterprise_id;
            $driver->names = strtolower($request->names);
            $driver->surnames = strtolower($request->surnames);
            $driver->phone = $request->phone;
            ( isset($request->vehicle_id) ? $driver->vehicle_id = $request->vehicle_id : "" );
            $driver->status = $request->status;
            $driver->save();

            DB::commit();

            return redirect()->route('drivers.index')->with('success', 'Conductor creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('drivers.create')->with('danger', 'Error al crear el conductor.');
        }
    }

    public function edit($request, $id){
        try {
            $driver = Driver::with('enterprise', 'destination')->where('id', $id)->first();
            return view('settings.drivers.new', [
                'breadcrumbs' => [
                    [
                        "route" => route("drivers.index"),
                        "name" => "Listado de conductores",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar conductor: ".$driver->name." ".$driver->surnames,
                        "active" => true
                    ]                    
                ],
                'enterprises' => Enterprise::whereIn('type_enterprise', ['PROVIDER', 'MAIN'])->get(),
                'driver' => $driver,
                'units' => $this->Units(),
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $driver = Driver::find($id);
            $driver ->enterprise_id = $request->enterprise_id;
            $driver->names = strtolower($request->names);
            $driver->surnames = strtolower($request->surnames);
            $driver->phone = $request->phone;
            ( isset($request->vehicle_id) ? $driver->vehicle_id = $request->vehicle_id : "" );
            $driver->status = $request->status;
            $driver->save();

            DB::commit();

            return redirect()->route('drivers.index')->with('success', 'Conductor actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('drivers.update', $id)->with('danger', 'Error al actualizar el conductor.');
        }
    }

    public function destroy($request, $id){
        try {
            DB::beginTransaction();
            $driver = Driver::find($id);
            $driver->delete();
            DB::commit();
            return redirect()->route('drivers.index')->with('success', 'Se elimimo correctamente el conductor.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('drivers.index')->with('danger', 'Error al eliminar el conductor.');
        }
    } 
}