<?php

namespace App\Repositories\Settings;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

use App\Models\Role;
use App\Models\RolesPermit;
use App\Models\UserRole;
use App\Models\Module;

class RoleRepository
{
    public function indexRoles($request)
    {
        try {
            return view('settings.roles.index', [
                'roles' => Role::select('id', 'role')->get(),
                'breadcrumbs' => [
                    [
                        "route" => "",
                        "name" => "Listado de roles",
                        "active" => true                        
                    ]
                ]
            ]);
        } catch (Exception $e) {
            return view('settings.roles.index', [
                'roles' => []
            ]);
        }
    }
    
    public function createRole($request){
        try {
            return view('settings.roles.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('roles.index'),
                        "name" => "Listado de roles",
                        "active" => true                        
                    ],
                    [
                        "route" => "",
                        "name" => "Nuevo Rol",
                        "active" => false                        
                    ],                
                ],            
                'v_type' => 1,
                'role' => new Role(),
                'modules' => Module::with('submodules')->get(),
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => $e->getMessage()]);
        }
    }

    public function editRole($request,$role){
        try {
            return view('settings.roles.new', [
                'breadcrumbs' => [
                    [
                        "route" => route('roles.index'),
                        "name" => "Listado de roles",
                        "active" => false                        
                    ],
                    [
                        "route" => "",
                        "name" => "Editar Rol: ".$role->role,
                        "active" => true                        
                    ],                
                ],
                'v_type' => 2,
                'role' => $role,
                'modules' => Module::with('submodules')->get(),
            ]);
        } catch (Exception $e) {
            return back()->withErrors(['danger' => $e->getMessage()]);
        }
    }

    public function storeRole($request){
        try {
            DB::beginTransaction();

            $role = Role::create(['role' => $request->role]); // Usar create() en lugar de new + save()

            $permits = collect($request->permits)->map(fn($permit) => [
                'role_id' => $role->id,
                'submodule_id' => $permit
            ])->toArray();

            RolesPermit::insert($permits); // Insertar en una sola consulta

            DB::commit();
            return response()->json([
                'success' => true, 
                'message' => 'Rol creado correctamente',
                'status' => Response::HTTP_OK
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateRole($request, $role){
        try {
            DB::beginTransaction();

            $role->update(['role' => $request->role]); // Usar update()

            RolesPermit::where('role_id', $role->id)->delete(); // Eliminar permisos en una sola consulta

            $permits = collect($request->permits)->map(fn($permit) => [
                'role_id' => $role->id,
                'submodule_id' => $permit
            ])->toArray();

            RolesPermit::insert($permits); // Insertar en una sola consulta

            DB::commit();
            return response()->json([
                'success' => true, 
                'status' => 'success',
                'message' => 'Rol actualizado correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteRole($role){
        try {
            // Verificar si el rol tiene usuarios asignados con exists() en lugar de get()
            if ($role->users()->exists()) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'El rol tiene usuarios asignados',                    
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $role->delete();

            DB::commit();
            return response()->json([
                'success' => true, 
                'status' => 'success',
                'message' => 'Rol eliminado correctamente',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
