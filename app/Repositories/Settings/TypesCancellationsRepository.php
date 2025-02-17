<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\TypesCancellation;

//FACADES
use Illuminate\Support\Facades\DB;

class TypesCancellationsRepository
{
    public function index($request)
    {
        try {
            return view('settings.types_cancellations.index', [
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de tipos de cancelaciones",
                        "active" => true
                    ]
                ],
                'cancellations' => TypesCancellation::all()
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            return view('settings.types_cancellations.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('config.types-cancellations.index'),
                        "name" => "Listado de tipos de cancelaciones",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo tipo de cancelación",
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

            $cancellation = new TypesCancellation();
            $cancellation->name_es = strtolower($request->name);
            $cancellation->name_en = strtolower($request->name);
            $cancellation->status = $request->status;
            $cancellation->save();

            DB::commit();

            return redirect()->route('config.types-cancellations.index')->with('success', 'Tipo de cancelación creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('config.types-cancellations.create')->with('danger', 'Error al crear el tipo de cancelación.');
        }
    }

    public function edit($request, $id){
        try {
            $cancellation = TypesCancellation::find($id);
            return view('settings.types_cancellations.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('config.types-cancellations.index'),
                        "name" => "Listado de tipos de cancelaciones",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar el tipo de cancelación: ".$cancellation->name_es,
                        "active" => true
                    ]
                ],
                'cancellation' => $cancellation,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $cancellation = TypesCancellation::find($id);
            $cancellation->name_es = strtolower($request->name);
            $cancellation->name_en = strtolower($request->name);
            $cancellation->status = $request->status;
            $cancellation->save();            

            DB::commit();

            return redirect()->route('config.types-cancellations.index')->with('success', 'Tipo de cancelación actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('config.types-cancellations.edit', $id)->with('danger', 'Error al actualizar el tipo de cancelación.');
        }
    }

    public function destroy($request, $id){
        try {
            $cancellation = TypesCancellation::find($id);
            $cancellation->delete();
            return redirect()->route('config.types-cancellations.index')->with('success', 'Se elimimo correctamente el tipo de cancelación.');
        } catch (Exception $e) {
        }
    }
}