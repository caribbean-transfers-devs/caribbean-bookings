<?php

namespace App\Repositories\Roles;

use App\Models\Module;
use App\Models\Role;
use App\Models\RolesPermit;
use App\Models\User;
use App\Models\UserRole;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class RoleRepository
{
    public function indexRoles($request)
    {
        try {
            
            $roles = Role::all();

            return view('roles.index', compact('roles'));
            
        } catch (\Throwable $e) {

            $roles = [];
            
            return view('roles.index', compact('roles'));
        }
    }
    

    public function createRole($request){
        return view('roles.create_edit', [
            'modules' => Module::all(),
            'v_type' => 1,
            'role' => new Role()
        ]);
    }

    public function editRole($request,$role){
        return view('roles.create_edit', [
            'modules' => Module::all(),
            'v_type' => 2,
            'role' => $role
        ]);
    }

    public function storeRole($request){
        try {
            DB::beginTransaction();

            $role = new Role();
            $role->role = $request->role;
            $role->save();

            foreach ($request->permits as $permit) {
                $role_permit = new RolesPermit();
                $role_permit->role_id = $role->id;
                $role_permit->submodule_id = $permit;
                $role_permit->save();
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Rol creado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al crear el usuario',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function updateRole($request, $role){
        try {
            DB::beginTransaction();

            $role->role = $request->role;
            $role->save();

            $role->permits()->delete();
            foreach ($request->permits as $permit) {
                $role_permit = new RolesPermit();
                $role_permit->role_id = $role->id;
                $role_permit->submodule_id = $permit;
                $role_permit->save();
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Rol actualizado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al actualizar el usuario',
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ]);
        }
    }

    public function deleteRole($role){
        try {
            //Check if roles has users
            $users = $role->users()->get();
            if(count($users) > 0){
                return response()->json([
                    'success' => false, 
                    'message' => 'El rol tiene usuarios asignados',
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::beginTransaction();

            $role->delete();

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Rol eliminado correctamente',
                'status' => Response::HTTP_OK
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false, 
                'message' => 'Error al eliminar la IP',
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
