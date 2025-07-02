<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use App\Models\Zones;
use App\Models\ZonesEnterprise;
use App\Models\OperatorFee;

//TRAITS
use App\Traits\RoleTrait;

class OperatorFeeController extends Controller
{
    use RoleTrait;
        
    protected function getAllZones()
    {
        $zones = Zones::select('id', 'name')->get()->map(function($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'type' => 'internal',
                'enterprise' => 'Caribbean Transfers'
            ];
        });
    
        $enterpriseZones = ZonesEnterprise::select('id', 'enterprise_id', 'name')
                            ->whereHas('enterprise', function($query) {
                                $query->where('type_enterprise', 'CUSTOMER');
                            })
                            ->with(['enterprise' => function($query) {
                                $query->select('id', 'names');
                            }])->get()->map(function($zone) {
            return [
                'id' => $zone->id,
                'name' => $zone->name,
                'type' => 'customer',
                'enterprise' => $zone->enterprise ? $zone->enterprise->names : null
            ];
        });

        return $zones->merge($enterpriseZones);
    }

    public function index()
    {
        if(!$this->hasPermission(129)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        // $fees = OperatorFee::all()->map(function($fee) {
        //     $fee->commission = $fee->calculateCommission();
        //     return $fee;
        // });
        $fees = OperatorFee::with('logs') // Cargar relaciones si es necesario
            ->paginate(10); // Paginar con 10 elementos por página
        
        // Calcular comisión para cada elemento
        $fees->getCollection()->transform(function($fee) {
            $fee->commission = $fee->calculateCommission();
            return $fee;
        });
        
        return view('settings.operator-fees.index-v2', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Listado de costos operativos",
                    "active" => true
                ]
            ],
            'allZones' => $this->getAllZones(),
            'fees' => $fees
        ]);
    }

    public function create()
    {
        if(!$this->hasPermission(130)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return view('settings.operator-fees.new', [ 
            'breadcrumbs' => [
                [
                    "route" => route("operator-fees.index"),
                    "name" => "Listado de costos operativos",
                    "active" => false
                ],
                [
                    "route" => "",
                    "name" => "Nuevo grupo",
                    "active" => true
                ]
            ],
            'allZones' => $this->getAllZones() 
        ]);
    }

    public function store(Request $request)
    {
        if(!$this->hasPermission(130)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'zone_ids' => 'required|array',
            'zone_ids.*' => [
                'integer',
                    Rule::unique('operator_fees', 'zone_ids->*')->where(function ($query) {
                    return $query->whereNotNull('zone_ids');
                })
            ]
        ]);

        $operatorFee = OperatorFee::create([
            'name' => $validated['name'],
            'base_amount' => $validated['base_amount'],
            'commission_percentage' => $validated['commission_percentage'],
            'zone_ids' => $validated['zone_ids']
        ]);

        return redirect()->route('operator-fees.index')
                         ->with('success', 'Tarifa creada exitosamente');
    }

    public function edit(OperatorFee $operatorFee)
    {
        if(!$this->hasPermission(131)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return view('settings.operator-fees.new', [ 
            'breadcrumbs' => [
                [
                    "route" => route("operator-fees.index"),
                    "name" => "Listado de costos operativos",
                    "active" => false
                ],
                [
                    "route" => "",
                    "name" => "Editar grupo: ".$operatorFee->name,
                    "active" => true
                ]
            ],
            'allZones' => $this->getAllZones(), 
            'operatorFee' => $operatorFee 
        ]);
    }

    public function update(Request $request, OperatorFee $operatorFee)
    {
        if(!$this->hasPermission(131)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'base_amount' => 'required|numeric|min:0',
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'zone_ids' => 'required|array',
            'zone_ids.*' => [
                'integer',
                Rule::unique('operator_fees', 'zone_ids->*')
                    ->where(function ($query) {
                        return $query->whereNotNull('zone_ids');
                    })
                    ->ignore($operatorFee->id)
            ]
        ]);

        $operatorFee->update($validated);

        return redirect()->route('operator-fees.index')
                         ->with('success', 'Tarifa actualizada exitosamente');
    }

    public function show(OperatorFee $operatorFee)
    {
        if(!$this->hasPermission(133)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return view('settings.operator-fees.show', [
            'allZones'      => $this->getAllZones(),
            'operatorFee'   => $operatorFee, 
            'logs'          => $operatorFee->logs()->with('user')->get()
        ]);
    }    

    public function destroy(OperatorFee $operatorFee)
    {
        if(!$this->hasPermission(132)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        $operatorFee->delete();
        return redirect()->route('operator-fees.index')
                         ->with('success', 'Tarifa eliminada exitosamente');
    }
}