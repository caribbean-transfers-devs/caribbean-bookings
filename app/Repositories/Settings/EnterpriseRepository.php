<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Enterprise;

//FACADES
use Illuminate\Support\Facades\DB;

class EnterpriseRepository
{
    public function index($request)
    {
        try {
            $enterprises = Enterprise::all();
            return view('settings.enterprises.index', [
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de empresas",
                        "active" => true
                    ]
                ],
                'enterprises' => $enterprises
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            return view('settings.enterprises.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nueva empresa",
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

            $enterprise = new Enterprise();
            $enterprise->names = strtolower($request->names);
            $enterprise->is_external = $request->is_external;
            $enterprise->status = $request->status;
            $enterprise->type_enterprise = $request->type_enterprise;
            $enterprise->save();

            DB::commit();

            // return response()->json([
            //     'success' => true, 
            //     'message' => 'Usuario creado correctamente',
            // ], Response::HTTP_CREATED);

            return redirect()->route('enterprises.index')->with('success', 'Empresa creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Error al crear el usuario',
            //     'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            // ]);

            return redirect()->route('enterprises.create')->with('danger', 'Error al crear la empresa.');
        }
    }

    public function edit($request, $id){
        try {
            $enterprise = Enterprise::find($id);
            return view('settings.enterprises.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar la empresa: ".$enterprise->names,
                        "active" => true
                    ]
                ],
                'enterprise' => $enterprise,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $enterprise = Enterprise::find($id);
            $enterprise->names = strtolower($request->names);
            $enterprise->is_external = $request->is_external;
            $enterprise->status = $request->status;
            $enterprise->type_enterprise = $request->type_enterprise;            
            $enterprise->save();

            DB::commit();

            // return response()->json([
            //     'success' => true, 
            //     'message' => 'Usuario creado correctamente',
            // ], Response::HTTP_CREATED);

            return redirect()->route('enterprises.index')->with('success', 'Empresa actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Error al crear el usuario',
            //     'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            // ]);

            return redirect()->route('enterprises.update', $id)->with('danger', 'Error al actualizar la empresa.');
        }
    }

    public function destroy($request, $id){
        try {
            $enterprise = Enterprise::find($id);
            $enterprise->delete();
            return redirect()->route('enterprises.index')->with('success', 'Se elimimo correctamente la empresa.');
        } catch (Exception $e) {
        }
    }    
}