<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\SalesType;

//FACADES
use Illuminate\Support\Facades\DB;

class TypesSalesRepository
{
    public function index($request)
    {
        try {
            return view('settings.types_sales.index', [
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de tipos de ventas",
                        "active" => true
                    ]
                ],
                'sales' => SalesType::all()
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request){
        try {
            return view('settings.types_sales.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('types.sales.index'),
                        "name" => "Listado de tipos de ventas",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo tipo de venta",
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

            $sale = new SalesType();
            $sale->name = ucwords(strtolower($request->name));
            $sale->rate_report = $request->rate_report;
            $sale->public_rate = $request->public_rate;
            $sale->status = $request->status;
            $sale->blocked = ( $request->status == 'private' ? 1 : 0 );
            $sale->type = $request->type;
            $sale->save();

            DB::commit();

            return redirect()->route('types.sales.index')->with('success', 'Tipo de venta creada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('types.sales.create')->with('danger', $e->getMessage());
        }
    }

    public function edit($request, $id){
        try {
            $sale = SalesType::find($id);
            return view('settings.types_sales.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('types.sales.index'),
                        "name" => "Listado de tipos de ventas",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Editar el tipo de venta: ".$sale->name,
                        "active" => true
                    ]
                ],
                'sale' => $sale,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $sale = SalesType::find($id);
            $sale->name = ucwords(strtolower($request->name));
            $sale->rate_report = $request->rate_report;
            $sale->public_rate = $request->public_rate;
            $sale->status = $request->status;
            $sale->blocked = ( $request->status == 'private' ? 1 : 0 );
            $sale->type = $request->type;
            $sale->save();            

            DB::commit();

            return redirect()->route('types.sales.index')->with('success', 'Tipo de venta actualizada correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('types.sales.edit', $id)->with('danger', $e->getMessage());
        }
    }

    public function destroy($request, $id){
        try {
            $sale = SalesType::find($id);
            $sale->delete();
            return redirect()->route('types.sales.index')->with('success', 'Se elimimo correctamente el tipo de venta.');
        } catch (Exception $e) {
        }
    }
}