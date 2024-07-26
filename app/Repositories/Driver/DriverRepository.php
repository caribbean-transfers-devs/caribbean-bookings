<?php

namespace App\Repositories\Driver;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Enterprise;
use App\Models\Driver;

//FACADES
use Illuminate\Support\Facades\DB;

class DriverRepository
{
    public function index($request)
    {
        try {
            $drivers = Driver::with('enterprise','destination')->get();
            return view('drivers.index', compact('drivers'));
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            $enterprises = Enterprise::all();
            return view('drivers.new', compact('enterprises'));
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
            $enterprises = Enterprise::all();
            $driver = Driver::find($id);
            return view('drivers.new',compact('enterprises','driver'));
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
            $driver = Driver::find($id);
            $driver->delete();
            return redirect()->route('drivers.index')->with('success', 'Se elimimo correctamente la empresa.');
        } catch (Exception $e) {
        }
    } 
}