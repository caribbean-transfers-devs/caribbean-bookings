<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use App\Models\Enterprise;
use App\Models\Destination;
use App\Models\ZonesEnterprise;
use App\Models\ZonesPointsEnterprise;

class ZonesEnterpriseRepository{

    public function index($request, $id = 0)
    {
        $zones = Enterprise::select(['id', 'names'])
                            ->with(['zones_enterprises' => function($query) use ($request) {
                                $query->select(['id', 'enterprise_id', 'destination_id', 'name', 'is_primary', 'status', 'iata_code', 'cut_off', 'cut_off_operation', 'distance', 'time'])
                                        ->when($request->filled('destination_id'), function($q) use ($request) {
                                            $q->where('destination_id', $request->destination_id);
                                        })                                        
                                        ->with(['destination' => function($subQuery) {
                                            $subQuery->select(['id', 'name']);
                                        }]);
                            }])
                            ->find($id);
                            
        try {
            return view('settings.zones_enterprise.index', [
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],
                    [
                        "route" => '',
                        "name" => "Zonas de la empresa: ".( isset($zones->names) ? $zones->names : 'NO DEFINIDO' ),
                        "active" => true
                    ]
                ],
                'zones' => $zones,
                'destinations' => Destination::all()
            ]);
        } catch (Exception $e) {
        }
    }
    
    public function create($request, $id = 0)
    {
        $enterprise = Enterprise::select(['id', 'names'])->find($id);

        try {
            return view('settings.zones_enterprise.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],                    
                    [
                        "route" => route('enterprises.zones.index', [( isset($enterprise->id) ? $enterprise->id : 0 )]),
                        "name" => "Zonas de la empresa: ".( isset($enterprise->names) ? $enterprise->names : 'NO DEFINIDO' ),
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Crear nueva zona",
                        "active" => true
                    ]
                ],
                "enterprise" => $enterprise,
                'destinations' => Destination::all()
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request, $id = 0)
    {
        try {
            DB::beginTransaction();

            $zone = new ZonesEnterprise();
            $zone->enterprise_id        = $id;
            $zone->destination_id       = $request->destination_id;
            $zone->name                 = strtolower($request->name);
            $zone->is_primary           = $request->is_primary ?? 0;
            $zone->status               = $request->status ?? 0;
            $zone->iata_code            = $request->iata_code ?? NULL;
            $zone->cut_off              = $request->cut_off ?? NULL;
            $zone->cut_off_operation    = $request->cut_off ?? NULL;
            $zone->distance             = $request->distance;
            $zone->time                 = $request->time;
            $zone->save();

            DB::commit();

            return redirect()->route('enterprises.zones.index', [$zone->enterprise_id])
                ->with('success', 'Zona creada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('danger', 'Error al crear la zona: ' . $e->getMessage());
        }
    }    

    public function edit($request, $id = 0)
    {
        $zone = ZonesEnterprise::select(['id', 'enterprise_id', 'destination_id', 'name', 'is_primary', 'status', 'iata_code', 'cut_off', 'cut_off_operation', 'distance', 'time'])
                        ->with(['enterprise' => function($query) {
                            $query->select(['id', 'names']);
                        }])
                        ->find($id);

        try {
            return view('settings.zones_enterprise.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],
                    [
                        "route" => route('enterprises.zones.index', [( isset($zone->enterprise->id) ? $zone->enterprise->id : 0 )]),
                        "name" => "Sitios de la empresa: ".( isset($zone->enterprise->names) ? $zone->enterprise->names : 'NO DEFINIDO' ),
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Actualizar zona: ".( isset($zone->name) ? $zone->name : 'NO DEFINIDO' ),
                        "active" => true
                    ]
                ],                
                'zone' => $zone,
                'destinations' => Destination::all()
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id = 0)
    {
        try {
            DB::beginTransaction();

            $zone = ZonesEnterprise::find($id);
            $zone->destination_id       = $request->destination_id;
            $zone->name                 = strtolower($request->name);
            $zone->is_primary           = $request->is_primary ?? 0;
            $zone->status               = $request->status ?? 0;
            $zone->iata_code            = $request->iata_code ?? NULL;
            $zone->cut_off              = $request->cut_off ?? NULL;
            $zone->cut_off_operation    = $request->cut_off ?? NULL;
            $zone->distance             = $request->distance;
            $zone->time                 = $request->time;
            $zone->save();

            DB::commit();

            return redirect()->route('enterprises.zones.index', [$zone->enterprise_id])
                ->with('success', 'Zona actualizada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('danger', 'Error al actualizar la zona: ' . $e->getMessage());
        }
    }

    public function destroy($request, $id = 0)
    {
        try {
            $zone = ZonesEnterprise::findOrFail($id);
            $zone->delete();

            return redirect()->route('enterprises.zones.index', [$zone->enterprise_id])
                ->with('success', 'Zona eliminada correctamente.');
        } catch (Exception $e) {
            return back()->with('danger', 'Error al eliminar la zona');
        }
    }

    public function points($request)
    {
        $data = DB::select("SELECT 
                                zon.id, 
                                zon.name, 
                                zp.latitude, 
                                zp.longitude
                            FROM zones_enterprises as zon
                                INNER JOIN zones_points_enterprises as zp ON zp.zone_id = zon.id
                                WHERE zon.destination_id = :destination_id",[
                                    'destination_id' => $request->id,
                                ]);
                                
        if(sizeof($data) <= 0):
            return response()->json([], 200);
        endif;

        $items = [];
        foreach($data as $key => $value):
            if( !isset($items[ $value->id ]) ):
                $items[ $value->id ] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "points" => []
                ];
            endif;

            $items[ $value->id ]['points'][] = [
                "lat" => $value->latitude,
                "lng" => $value->longitude,
            ];
        endforeach;

        return response()->json($items, 200);
    }

    public function setpoints($request)
    {
        try {
            DB::beginTransaction();
            
            $delete = ZonesPointsEnterprise::where('zone_id', $request->id)->delete();
            if(sizeof($request->coordinates) >= 1):
                foreach($request->coordinates as $key => $value):
                    $point = new ZonesPointsEnterprise;
                    $point->zone_id = $request->id;
                    $point->latitude = $value['lat'];
                    $point->longitude = $value['lng'];
                    $point->save();                        
                endforeach;
            endif;

            DB::commit();

            return response()->json([
                'message' => 'Geocerca actualizada con éxito',
                'success' => true
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Hubo un error, contacte a soporte',
                'success' => false
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }        
    }
}