<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Enterprise;
use App\Models\Vehicle;
use App\Models\DestinationService;

//FACADES
use Illuminate\Support\Facades\DB;

class VehicleRepository
{
    public function index($request)
    {
        try {
            $vehicles = Vehicle::with('enterprise','destination_service','destination')->get();
            return view('settings.vehicles.index', [
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de vehículos",
                        "active" => true
                    ]
                ],
                'vehicles' => $vehicles,
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            return view('settings.vehicles.new', [
                'breadcrumbs' => [
                    [
                        "route" => route("vehicles.index"),
                        "name" => "Listado de vehículos",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo vehículo",
                        "active" => true
                    ]                    
                ],
                'enterprises' => Enterprise::whereIn('type_enterprise', ['PROVIDER', 'MAIN'])->get(),
                'services' => DestinationService::with('destination')->get()
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request){
        try {
            DB::beginTransaction();

            $vehicle = new Vehicle();
            $vehicle->enterprise_id = $request->enterprise_id;
            $vehicle->destination_service_id = $request->destination_service_id;
            $vehicle->name = $request->name;
            $vehicle->unit_code = $request->unit_code;
            $vehicle->plate_number = $request->plate_number;
            $vehicle->status = $request->status;
            $vehicle->save();

            DB::commit();

            return redirect()->route('vehicles.index')->with('success', 'Vehículo creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('vehicles.create')->with('danger', 'Error al crear el vehículo.');
        }
    }

    public function edit($request, $id){
        $vehicle = Vehicle::with('enterprise', 'destination_service', 'destination')->where('id', $id)->first();
        try {
            return view('settings.vehicles.new', [
                'breadcrumbs' => [
                    [
                        "route" => route("vehicles.index"),
                        "name" => "Listado de vehículos",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar vehículo: ".$vehicle->name,
                        "active" => true
                    ]                    
                ],
                'enterprises' => Enterprise::whereIn('type_enterprise', ['PROVIDER', 'MAIN'])->get(),
                'services' => DestinationService::with('destination')->get(),
                'vehicle' => $vehicle,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $vehicle = Vehicle::find($id);
            $vehicle->enterprise_id = $request->enterprise_id;
            $vehicle->destination_service_id = $request->destination_service_id;
            $vehicle->name = $request->name;
            $vehicle->unit_code = $request->unit_code;
            $vehicle->plate_number = $request->plate_number;
            $vehicle->status = $request->status;
            $vehicle->save();

            DB::commit();

            return redirect()->route('vehicles.index')->with('success', 'Vehículo actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('vehicles.edit', [$id])->with('danger', 'Error al actualizar el vehículo.');
        }
    }

    public function destroy($request, $id){
        try {
            DB::beginTransaction();
            $vehicle = Vehicle::find($id);
            $vehicle->delete();
            DB::commit();
            return redirect()->route('vehicles.index')->with('success', 'Se elimimo correctamente el vehículo.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('vehicles.index')->with('danger', 'Error al eliminar el vehículo.');
        }
    } 
}