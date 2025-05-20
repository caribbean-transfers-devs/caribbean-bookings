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
            $enterprise->is_external = $request->is_external;

            $enterprise->names = strtolower($request->names);
            $enterprise->address = strtolower($request->address);
            $enterprise->phone = $request->phone;
            $enterprise->email = strtolower($request->email);
            $enterprise->company_contact = $request->company_contact ?? NULL;
            $enterprise->credit_days = $request->credit_days ?? 0;

            $enterprise->company_name_invoice = $request->company_name_invoice ?? NULL;
            $enterprise->company_rfc_invoice = $request->company_rfc_invoice ?? NULL;
            $enterprise->company_address_invoice = $request->company_address_invoice ?? NULL;
            $enterprise->company_email_invoice = $request->company_email_invoice ?? NULL;
            
            $enterprise->is_invoice_iva = $request->is_invoice_iva ?? 0;
            $enterprise->is_rates_iva = $request->is_rates_iva ?? 0;
            $enterprise->is_foreign = $request->is_foreign ?? 0;
            $enterprise->currency = $request->currency ?? 'MXN';
            $enterprise->status = $request->status;
            $enterprise->type_enterprise = $request->type_enterprise;
            $enterprise->save();

            DB::commit();

            return redirect()->route('enterprises.index')->with('success', 'Empresa creada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('enterprises.create')->with('danger', 'Error al crear la empresa.');
        }
    }

    public function edit($request, $id){
        try {
            $enterprise = Enterprise::select('id','names','address','phone','email','company_contact','credit_days','company_name_invoice','company_rfc_invoice','company_address_invoice','company_email_invoice','is_invoice_iva','is_rates_iva','is_foreign','currency','status','destination_id')
                                    ->with(['destination' => function($query) {
                                        $query->select(['id', 'name']);
                                    }])
                                    ->with(['sites' => function($query) {
                                        $query->select(['id', 'name', 'color', 'transactional_email_send', 'is_commissionable', 'is_cxc', 'is_cxp', 'type_site', 'enterprise_id']);
                                    }])
                                    ->find($id);

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
            $enterprise->is_external = $request->is_external;

            $enterprise->names = strtolower($request->names);
            $enterprise->address = strtolower($request->address);
            $enterprise->phone = $request->phone;
            $enterprise->email = strtolower($request->email);
            $enterprise->company_contact = $request->company_contact ?? NULL;
            $enterprise->credit_days = $request->credit_days ?? 0;

            $enterprise->company_name_invoice = $request->company_name_invoice ?? NULL;
            $enterprise->company_rfc_invoice = $request->company_rfc_invoice ?? NULL;
            $enterprise->company_address_invoice = $request->company_address_invoice ?? NULL;
            $enterprise->company_email_invoice = $request->company_email_invoice ?? NULL;
            
            $enterprise->is_invoice_iva = $request->is_invoice_iva ?? 0;
            $enterprise->is_rates_iva = $request->is_rates_iva ?? 0;
            $enterprise->is_foreign = $request->is_foreign ?? 0;
            $enterprise->currency = $request->currency ?? 'MXN';
            $enterprise->status = $request->status;
            $enterprise->type_enterprise = $request->type_enterprise;        
            $enterprise->save();

            DB::commit();

            return redirect()->route('enterprises.index')->with('success', 'Empresa actualizada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

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