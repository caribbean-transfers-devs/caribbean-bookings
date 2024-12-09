<?php

namespace App\Repositories\Exchange;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//MODELS
use App\Models\ExchangeRateReport;

class ExchangeReportsRepository{
    public function index($request){
        return view('settings.exchanges.index', [            
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Listado de tipos de cambio",
                    "active" => true
                ]
            ],
            'exchanges' => ExchangeRateReport::all(),
        ]);
    }

    public function create($request){
        try {
            return view('settings.exchanges.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('config.exchanges'),
                        "name" => "Listado de tipos de cambio",
                        "active" => false
                    ],                    
                    [
                        "route" => "",
                        "name" => "Nuevo tipo de cambio",
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

            $exchange = new ExchangeRateReport();
            $exchange ->exchange = $request->exchange;
            $exchange->date_init = $request->date_init;
            $exchange->date_end = $request->date_end;
            $exchange->save();

            DB::commit();

            return redirect()->route('config.exchanges')->with('success', 'Tipo de cambio creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('config.exchanges')->with('danger', 'Error al crear el tipo de cambio.');
        }
    }

    public function edit($request, $id){
        try {
            $exchange = ExchangeRateReport::find($id);
            return view('settings.exchanges.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('config.exchanges'),
                        "name" => "Listado de tipos de cambio",
                        "active" => false
                    ],                    
                    [
                        "route" => "",
                        "name" => "Editar tipo de cambio: ".$exchange->exchange,
                        "active" => true
                    ]
                ],
                'exchange' => $exchange,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id){
        try {
            DB::beginTransaction();

            $exchange = ExchangeRateReport::find($id);
            $exchange->exchange = $request->exchange;
            $exchange->date_init = $request->date_init;
            $exchange->date_end = $request->date_end;
            $exchange->save();

            DB::commit();

            return redirect()->route('config.exchanges')->with('success', 'Tipo de cambio actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('config.exchanges.update', $id)->with('danger', 'Error al actualizar el tipo de cambio.');
        }
    }

    public function destroy($request, $id){
        try {
            $exchange = ExchangeRateReport::find($id);
            $exchange->delete();
            return redirect()->route('config.exchanges')->with('success', 'Se elimimo correctamente el tipo de cambio.');
        } catch (Exception $e) {
            return redirect()->route('config.exchanges')->with('danger', 'Error al eliminar el tipo de cambio.');
        }
    }
}