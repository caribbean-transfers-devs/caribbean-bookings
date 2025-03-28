<?php

namespace App\Traits;

use App\Models\RolesPermit;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait RoleTrait
{
    
    /**
     * Summary of getRolesAndSubmodules
     * Obtenemos de los modelos las relaciones y con estas creamos 
     * una variable tipo array en el cual retornaremos un array 
     * de roles y submodulos a los que tiene acceso.
     * @return array
     */
    public function getRolesAndSubmodules(){
        //Validamos si existe una sessión abierta y obtenemos el id, en caso contrario retornamos 0
        // $user_id = (Auth::check()) ? Auth::id() : 0;
        $user_id = Auth::id() ?? 0;
    
        if (!$user_id) {
            return [];
        }        
        
        // $roles = UserRole::where('user_id', $user_id)->pluck('role_id')->toArray();

        // $permissions_data = [];
        // if (count($roles) > 0){
        //     $permissions_data['roles'] = $roles;
        //     $roles_permits = UserRole::where('user_id', $user_id)->leftJoin('roles_permits', 'roles_permits.role_id', '=', 'user_roles.role_id')
        //     ->distinct()->pluck('submodule_id')->toArray();
        //     $permissions_data['permissions'] = $roles_permits;
        // }

        // return $permissions_data;

        // Una sola consulta para obtener ambos datos
        $data = UserRole::where('user_id', $user_id)
            ->with(['role.permissions']) // Asume relación Eloquent
            ->get()
            ->reduce(function ($carry, $userRole) {
                $carry['roles'][] = $userRole->role_id;
                foreach ($userRole->role->permissions ?? [] as $permission) {
                    $carry['permissions'][] = $permission->submodule_id;
                }
                return $carry;
            }, ['roles' => [], 'permissions' => []]);

        // Eliminar duplicados
        $data['roles'] = array_unique($data['roles']);
        $data['permissions'] = array_unique($data['permissions']);

        return $data;        
    }

    /**
     * Summary of hasPermission
     * @param int $submodule_id id del Submodulo obtenido de la DB al cual se quiere acceder
     * @return bool Retorna true or false para autorizar o negar el permiso respectivamente.
     */
    public static function hasPermission2(int $submodule_id){
        $roles = session()->get('roles');
        if(isset($roles) && $submodule_id > 0){
            if(in_array($submodule_id,$roles['permissions'])){
                return true;
            }else{
                return false;
            }
        }

        return false;
    }

    public function hasPermission(int $submodule_id): bool
    {
        $roles = session('roles');
    
        return !empty($roles) && in_array($submodule_id, $roles['permissions'] ?? []);
    } 

    /**
     * Summary of hasPermission
     * @param int $role id del rol obtenido de la DB al cual se quiere acceder
     * @return bool Retorna true or false para autorizar o negar el permiso respectivamente.
     */
    public static function hasRole(int $role): bool
    {
        // $roles = session()->get('roles');
        // if(isset($roles) && $role > 0){
        //     if(in_array($role,$roles['roles'])){
        //         return true;
        //     }else{
        //         return false;
        //     }
        // }

        // return false;

        if ($role <= 0) {
            return false;
        }
    
        $roles = session('roles', []);
        
        if (empty($roles)) {
            $roles = self::getRolesAndSubmodules();
            session(['roles' => $roles]);
        }
    
        return in_array($role, $roles['roles'] ?? []);        
    }

    /**
     * Summary of RolehasPermission
     * @param int $Role id del Rol que quieres acceder
     * @param int $submodule_id id del Submodulo obtenido de la DB al cual se quiere acceder
     * @return bool Retorna true or false para autorizar o negar el permiso respectivamente.
     */
    public static function RolehasPermission(int $role, int $submodule_id): bool
    {
        // if($Role && $submodule_id > 0){
        //     $check = DB::table('roles_permit')->where('role',$Role)->where('submodule_id',$submodule_id)->first();
        //     if($check){
        //         return true;
        //     }else{
        //         return false;
        //     }
        // }

        // return false;

        if ($role <= 0 || $submodule_id <= 0) {
            return false;
        }
    
        // Usar exists() en lugar de first() para mejor performance
        return DB::table('roles_permit')
            ->where('role', $role)
            ->where('submodule_id', $submodule_id)
            ->exists();        
    }

    /**
     * Summary of getPermissionForAPI
     * Método que permite consultar y devolver un array con todos los permisos asociados por sus diferentes roles
     * @param int $user_id id del usuario que quieres consultar 
     */
    public static function getPermissionForAPI(int $user_id){
        // if($user_id){
        //     $roles_permits = UserRole::where('users_id', $user_id)->leftJoin('roles_permits', 'roles_permits.role_id', '=', 'user_roles.role_id')->get();

        //     $permissions_data = [];

        //     if (count($roles_permits) > 0){
        //         foreach ($roles_permits as $r_p) {
        //             if(!in_array($r_p->submodules_id, $permissions_data)){
        //                 $permissions_data[] = $r_p->submodules_id;
        //             }                    
        //         }
        //     }

        //     return $permissions_data;
        // }

        // return [];

        if ($user_id <= 0) {
            return [];
        }
    
        return UserRole::where('users_id', $user_id)
            ->with(['role.permissions']) // Carga eager
            ->get()
            ->flatMap(function ($userRole) {
                return $userRole->role->permissions->pluck('submodules_id');
            })
            ->unique()
            ->values()
            ->toArray();        
    }
}
