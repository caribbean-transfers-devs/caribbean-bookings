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
    public function index($request)
    {
        try {
            return view('settings.sites.index', [
                'breadcrumbs' => [
                    [
                        "route" => '',
                        "name" => "Empresas",
                        "active" => false
                    ],                    
                ],
                'sites' => Site::with('enterprise')->get()
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request)
    {
        try {
            return view('settings.sites.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('sites.index'),
                        "name" => "Listado de sitios",
                        "active" => true
                    ],
                    [
                        "route" => "",
                        "name" => "Crear un nuevo sitio",
                        "active" => false
                    ]
                ],
                "enterprises" => Enterprise::all(),
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $Site = new Site();
            $Site->name = strtolower($request->name);
            $Site->logo = $request->logo;
            $Site->payment_domain = $request->payment_domain;
            $Site->color = $request->color;
            $Site->transactional_email = $request->transactional_email;
            $Site->transactional_email_send = $request->transactional_email_send;
            $Site->transactional_phone = $request->transactional_phone;
            $Site->is_commissionable = $request->is_commissionable;
            $Site->is_cxc = $request->is_cxc;
            $Site->is_cxp = $request->is_cxp;
            $Site->success_payment_url = $request->success_payment_url;
            $Site->cancel_payment_url = $request->cancel_payment_url;
            $Site->type_site = $request->type_site;
            $Site->enterprise_id = $request->enterprise_id;
            $Site->save();

            DB::commit();

            return redirect()->route('sites.index')->with('success', 'Sitio creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('sites.create')->with('danger', 'Error al crear el sitio.');
        }
    }    

    public function edit($request, $Site)
    {
        try {
            return view('settings.sites.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('sites.index'),
                        "name" => "Listado de sitios",
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Actualizar sitio: ".$Site->name,
                        "active" => true
                    ]
                ],                
                'site' => $Site,
                "enterprises" => Enterprise::all(),
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $Site)
    {
        try {
            DB::beginTransaction();

            $Site->name = strtolower($request->name);
            $Site->logo = $request->logo;
            $Site->payment_domain = $request->payment_domain;
            $Site->color = $request->color;
            $Site->transactional_email = $request->transactional_email;
            $Site->transactional_email_send = $request->transactional_email_send;
            $Site->transactional_phone = $request->transactional_phone;
            $Site->is_commissionable = $request->is_commissionable;
            $Site->is_cxc = $request->is_cxc;
            $Site->is_cxp = $request->is_cxp;
            $Site->success_payment_url = $request->success_payment_url;
            $Site->cancel_payment_url = $request->cancel_payment_url;
            $Site->type_site = $request->type_site;
            $Site->enterprise_id = $request->enterprise_id;
            $Site->save();

            DB::commit();

            return redirect()->route('sites.index')->with('success', 'Sitio actualizado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('sites.update', $Site->id)->with('danger', 'Error al actualizar sitio.');
        }
    }

    public function destroy($request, $Site)
    {
        try {
            $Site->delete();
            return redirect()->route('sites.index')->with('success', 'Se elimimo correctamente el sitio.');
        } catch (Exception $e) {
        }
    }    
}