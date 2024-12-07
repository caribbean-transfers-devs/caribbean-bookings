<?php

namespace App\Repositories\Sites;

use Exception;
use Illuminate\Http\Response;

//MODELS
use App\Models\Site;

//FACADES
use Illuminate\Support\Facades\DB;

class SitesRepository
{
    public function index($Enterprise)
    {
        try {            
            $sites = Site::Where('enterprise_id', $Enterprise->id)->get();
            return view('sites.index', [
                'breadcrumbs' => [
                    [
                        "route" => '/enterprises',
                        "name" => "Empresas",
                        "active" => false
                    ],                    
                    [
                        "route" => "",
                        "name" => "Sitios de la empresa: " . $Enterprise->names,
                        "active" => true
                    ]
                ],
                'enterprise' => $Enterprise,
                'sites' => $sites
            ]);
        } catch (Exception $e) {
        }
    }

    public function create($request, $Enterprise){
        try {
            return view('sites.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('enterprise.sites', $Enterprise->id),
                        "name" => "Sitios de: ".$Enterprise->names,
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Crear un nuevo sitio",
                        "active" => true
                    ]
                ],
                "enterprise" => $Enterprise
            ]);
        } catch (Exception $e) {
        }
    }

    public function store($request, $Enterprise){
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
            $Site->enterprise_id = $Enterprise->id;
            $Site->save();

            DB::commit();

            // return response()->json([
            //     'success' => true, 
            //     'message' => 'Usuario creado correctamente',
            // ], Response::HTTP_CREATED);

            return redirect()->route('enterprise.sites', $Enterprise->id)->with('success', 'Sitio creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Error al crear el usuario',
            //     'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            // ]);

            return redirect()->route('sites.create')->with('danger', 'Error al crear el sitio.');
        }
    }

    public function edit($request, $Site){
        try {
            return view('sites.new',[
                'breadcrumbs' => [
                    [
                        "route" => route('enterprise.sites', $Site->enterprise_id),
                        "name" => "Sitios de: ".$Site->enterprise->names,
                        "active" => false
                    ],
                    [
                        "route" => "",
                        "name" => "Actualizar un nuevo sitio: ".$Site->name,
                        "active" => true
                    ]
                ],                
                'site' => $Site
            ]);
        } catch (Exception $e) {
        }
    }

    public function update($request, $Site){
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
            $Site->save();

            DB::commit();

            // return response()->json([
            //     'success' => true, 
            //     'message' => 'Usuario creado correctamente',
            // ], Response::HTTP_CREATED);

            return redirect()->route('enterprise.sites', $Site->enterprise_id)->with('success', 'Sitio creado correctamente.');

        } catch (Exception $e) {
            DB::rollBack();

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Error al crear el usuario',
            //     'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            // ]);

            return redirect()->route('enterprise.sites', $Site->enterprise_id)->with('danger', 'Error al actualizar sitio.');
        }
    }

    public function destroy($request, $Site){
        try {
            $Site->delete();
            return redirect()->route('enterprise.sites', $Site->enterprise_id)->with('success', 'Se elimimo correctamente el sitio.');
        } catch (Exception $e) {
        }
    }    
}