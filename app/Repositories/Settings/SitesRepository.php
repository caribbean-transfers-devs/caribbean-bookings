<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Enterprise;
use App\Models\Site;

//FACADES
use Illuminate\Support\Facades\DB;

class SitesRepository
{
    public function index($request, $id)
    {
        $sites = Enterprise::select(['id', 'names'])
                            ->with(['sites' => function($query) {
                                $query->select(['id', 'name', 'logo', 'payment_domain', 'color', 'transactional_email', 'transactional_email_send', 'transactional_phone', 'is_commissionable', 'is_cxc', 'is_cxp', 'success_payment_url', 'cancel_payment_url', 'type_site', 'enterprise_id']);
                            }])
                            ->find($id);

        try {
            return view('settings.sites.index', [
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],
                    [
                        "route" => '',
                        "name" => "Sitios de la empresa: ".( isset($sites->names) ? $sites->names : 'NO DEFINIDO' ),
                        "active" => true
                    ]
                ],
                'sites' => $sites
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request, $id = 0)
    {
        $enterprise = Enterprise::select(['id', 'names'])->find($id);

        try {
            return view('settings.sites.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],                    
                    [
                        "route" => route('enterprises.sites.index', [( isset($enterprise->id) ? $enterprise->id : 0 )]),
                        "name" => "Sitios de la empresa: ".( isset($enterprise->names) ? $enterprise->names : 'NO DEFINIDO' ),
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Crear un nuevo sitio",
                        "active" => true
                    ]
                ],
                "enterprise" => $enterprise,
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request, $id = 0)
    {
        try {
            DB::beginTransaction();

            $site = new Site();
            $site->name = strtolower($request->name);
            $site->logo = $request->logo;
            $site->payment_domain = $request->payment_domain;
            $site->color = $request->color;
            $site->transactional_email = $request->transactional_email;
            $site->transactional_email_send = $request->transactional_email_send;
            $site->transactional_phone = $request->transactional_phone;
            $site->is_commissionable = $request->is_commissionable;
            $site->is_cxc = $request->is_cxc;
            $site->is_cxp = $request->is_cxp;
            $site->success_payment_url = $request->success_payment_url;
            $site->cancel_payment_url = $request->cancel_payment_url;
            $site->type_site = $request->type_site;
            $site->enterprise_id = $id;
            $site->save();

            DB::commit();

            return redirect()->route('enterprises.sites.index', [$site->enterprise_id])
                ->with('success', 'Sitio creado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('danger', 'Error al crear el sitio: ' . $e->getMessage());
        }
    }    

    public function edit($request, $id = 0)
    {
        $site = Site::select(['id', 'name', 'logo', 'payment_domain', 'color', 'transactional_email', 'transactional_email_send', 'transactional_phone', 'is_commissionable', 'is_cxc', 'is_cxp', 'success_payment_url', 'cancel_payment_url', 'type_site', 'enterprise_id'])
                        ->with(['enterprise' => function($query) {
                            $query->select(['id', 'names']);
                        }])
                        ->find($id);

        try {
            return view('settings.sites.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('enterprises.index'),
                        "name" => "Listado de empresas",
                        "active" => false
                    ],
                    [
                        "route" => route('enterprises.sites.index', [( isset($site->enterprise->id) ? $site->enterprise->id : 0 )]),
                        "name" => "Sitios de la empresa: ".( isset($site->enterprise->names) ? $site->enterprise->names : 'NO DEFINIDO' ),
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Actualizar sitio: ".( isset($site->name) ? $site->name : 'NO DEFINIDO' ),
                        "active" => true
                    ]
                ],                
                'site' => $site,
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $id = 0)
    {
        try {
            DB::beginTransaction();

            $site = Site::find($id);
            $site->name = strtolower($request->name);
            $site->logo = $request->logo;
            $site->payment_domain = $request->payment_domain;
            $site->color = $request->color;
            $site->transactional_email = $request->transactional_email;
            $site->transactional_email_send = $request->transactional_email_send;
            $site->transactional_phone = $request->transactional_phone;
            $site->is_commissionable = $request->is_commissionable;
            $site->is_cxc = $request->is_cxc;
            $site->is_cxp = $request->is_cxp;
            $site->success_payment_url = $request->success_payment_url;
            $site->cancel_payment_url = $request->cancel_payment_url;
            $site->type_site = $request->type_site;
            $site->save();

            DB::commit();

            return redirect()->route('enterprises.sites.index', [$site->enterprise_id])
                ->with('success', 'Sitio actualizado correctamente.');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('danger', 'Error al actualizar el sitio: ' . $e->getMessage());
        }
    }

    public function destroy($request, $id = 0)
    {
        try {
            $site = Site::findOrFail($id);
            $site->delete();

            return redirect()->route('enterprises.sites.index', [$site->enterprise_id])
                ->with('success', 'Sitio eliminado correctamente.');
        } catch (Exception $e) {
            return back()->with('danger', 'Error al eliminar la empresa');
        }
    }    
}