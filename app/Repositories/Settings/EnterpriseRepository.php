<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

use App\Models\Enterprise;
use App\Models\EnterprisesMedia;

class EnterpriseRepository
{
    public function index()
    {
        try {
            $enterprises = Enterprise::query()
                ->select()
                ->withCount([
                    'sites',
                    'zones_enterprises',
                    'rates_enterprises',
                    'vehicles',
                    'drivers',
                ])                
                ->orderBy('is_external', 'ASC')
                ->get();

            // dd($enterprises->toArray());

            return view('settings.enterprises.index', [
                'breadcrumbs' => [
                    ["name" => "Listado de empresas", "active" => true]
                ],
                'enterprises' => $enterprises
            ]);
        } catch (Exception $e) {
            return back()->with('danger', 'Error al cargar el listado de empresas');
        }
    }

    public function create()
    {
        try {
            return view('settings.enterprises.new', [
                'breadcrumbs' => [
                    ["route" => route('enterprises.index'), "name" => "Listado de empresas", "active" => false],
                    ["name" => "Nueva empresa", "active" => true]
                ],
                'enterprise' => null
            ]);
        } catch (Exception $e) {
            return redirect()->route('enterprises.index')
                    ->with('danger', 'Error al cargar el formulario de creación');
        }
    }

    public function store($request): RedirectResponse
    {
        try {
            $data = $request->validated();
            
            DB::beginTransaction();

            $enterprise = new Enterprise();
            // $enterprise->is_external = $request->is_external;

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
            $enterprise->status = $request->status ?? 1;
            $enterprise->type_enterprise = $request->type_enterprise ?? 'PROVIDER';
            $enterprise->save();

            DB::commit();

            return redirect()->route('enterprises.index')
                ->with('success', 'Empresa creada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('danger', 'Error al crear la empresa: ' . $e->getMessage());
        }
    }

    public function edit($request, $id)
    {
        try {
            $enterprise = Enterprise::findOrFail($id);

            return view('settings.enterprises.new', [
                'breadcrumbs' => [
                    ["route" => route('enterprises.index'), "name" => "Listado de empresas", "active" => false],
                    ["name" => "Editar: " . $enterprise->names, "active" => true]
                ],
                'enterprise' => $enterprise
            ]);
        } catch (Exception $e) {
            return redirect()->route('enterprises.index')
                ->with('danger', 'Empresa no encontrada');
        }
    }

    public function update($request, $id): RedirectResponse
    {
        try {
            $data = $request->validated();
            
            DB::beginTransaction();

            $enterprise = Enterprise::find($id);
            // $enterprise->is_external = $request->is_external;

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
            $enterprise->status = $request->status ?? 1;
            $enterprise->type_enterprise = $request->type_enterprise ?? 'PROVIDER';
            $enterprise->save();

            DB::commit();

            return redirect()->route('enterprises.index')
                    ->with('success', 'Empresa actualizada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                    ->with('danger', 'Error al actualizar la empresa: ' . $e->getMessage());
        }
    }

    public function destroy($request, $id = 0): RedirectResponse
    {
        DB::beginTransaction();
        try {
            $enterprise = Enterprise::withTrashed()->find($id);
            if (!$enterprise) {
                return back()->withInput()
                        ->with('danger', 'La empresa no existe.');
            }

            // Actualizar estados antes de eliminar
            $enterprise->sites()->update(['status' => 0]); // Desactivar sitios
            $enterprise->vehicles()->update(['status' => 0]); // Desactivar vehículos
            $enterprise->drivers()->update(['status' => 0]); // Desactivar conductores
            
            // Eliminar la empresa (soft delete)            
            $enterprise->delete();

            DB::commit();
            
            return redirect()->route('enterprises.index')
                ->with('success', 'Empresa eliminada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                    ->with('danger', 'Error al eliminar la empresa: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene los medios asociados a una reservación
     *
     * @param mixed $request
     * @return \Illuminate\Contracts\View\View
     */
    public function getMedia($request)
    {
        $query = EnterprisesMedia::where('enterprise_id', $request->id)
                                    ->orderBy('id', 'desc');

        $media = $query->get();
        return view('settings.enterprises.media', compact('media'));
    }
}