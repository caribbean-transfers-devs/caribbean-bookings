<?php

namespace App\Repositories\Vehicle;

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
            return view('vehicles.index', [
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
            $enterprises = Enterprise::all();
            $services = DestinationService::all();
            return view('vehicles.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('vehicles.index'),
                        "name" => "Listado de vehículos",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo vehículo",
                        "active" => true
                    ]
                ],                
                'enterprises' => $enterprises, 
                'services' => $services,
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
            $vehicle->save();

            DB::commit();

            return redirect()->route('vehicles.index')->with('success', 'Vehículo creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('vehicles.create')->with('danger', 'Error al crear el vehículo.');
        }
    }

    public function edit($request, $id){
        try {
            $enterprises = Enterprise::all();           
            $services = DestinationService::all();            
            $vehicle = Vehicle::find($id);
            return view('vehicles.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('vehicles.index'),
                        "name" => "Listado de vehículos",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar el vehículo: ".$vehicle->name,
                        "active" => true
                    ]
                ],                
                'enterprises' => $enterprises, 
                'services' => $services, 
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
            $vehicle = Vehicle::find($id);
            $vehicle->delete();
            return redirect()->route('vehicles.index')->with('success', 'Se elimimo correctamente el vehículo.');
        } catch (Exception $e) {
        }
    } 
}