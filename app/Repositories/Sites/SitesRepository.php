<?php

namespace App\Repositories\Sites;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Site;

//FACADES
use Illuminate\Support\Facades\DB;

class SitesRepository
{
    public function index($request)
    {
        try {
            $enterprises = Enterprise::all();
            return view('sites.index', compact('enterprises'));
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            return view('sites.new');
        } catch (Exception $e) {
        }
    }

    public function store($request){
        try {
            DB::beginTransaction();

            $enterprise = new Enterprise();
            $enterprise->names = strtolower($request->names);
            $enterprise->is_external = $request->is_external;
            $enterprise->save();

            DB::commit();

            // return response()->json([
            //     'success' => true, 
            //     'message' => 'Usuario creado correctamente',
            // ], Response::HTTP_CREATED);

            return redirect()->route('sites.index')->with('success', 'Empresa creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Error al crear el usuario',
            //     'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            // ]);

            return redirect()->route('sites.create')->with('danger', 'Error al crear la empresa.');
        }
    }

    public function edit($request, $id){
        try {
            $enterprise = Enterprise::find($id);
            return view('sites.new',compact('enterprise'));
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $enterprise = Enterprise::find($id);
            $enterprise->names = strtolower($request->names);
            $enterprise->is_external = $request->is_external;
            $enterprise->save();

            DB::commit();

            // return response()->json([
            //     'success' => true, 
            //     'message' => 'Usuario creado correctamente',
            // ], Response::HTTP_CREATED);

            return redirect()->route('sites.index')->with('success', 'Empresa actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Error al crear el usuario',
            //     'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            // ]);

            return redirect()->route('sites.update', $id)->with('danger', 'Error al actualizar la empresa.');
        }
    }

    public function destroy($request, $id){
        try {
            $enterprise = Enterprise::find($id);
            $enterprise->delete();
            return redirect()->route('sites.index')->with('success', 'Se elimimo correctamente la empresa.');
        } catch (Exception $e) {
        }
    }    
}