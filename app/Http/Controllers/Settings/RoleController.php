<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORYS
use App\Repositories\Settings\RoleRepository;

//TRAITS
use App\Traits\RoleTrait;

//REQUEST
use App\Http\Requests\RoleRequest;

//MODELS
use App\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request, RoleRepository $roleRepository){
        if(RoleTrait::hasPermission(6)){
            return $roleRepository->indexRoles($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function create(Request $request,RoleRepository $roleRepository){
        if(RoleTrait::hasPermission(7)){
            return $roleRepository->createRole($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }          
    }

    public function edit(Request $request, Role $role, RoleRepository $roleRepository){
        if(RoleTrait::hasPermission(8)){
            return $roleRepository->editRole($request,$role);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }        
    }

    public function store(RoleRequest $request, RoleRepository $roleRepository){
        if(RoleTrait::hasPermission(7)){
            return $roleRepository->storeRole($request);
        }
    }

    public function update(RoleRequest $request, Role $role, RoleRepository $roleRepository){
        if(RoleTrait::hasPermission(8)){
            return $roleRepository->updateRole($request,$role);
        }
    }

    public function destroy(Role $role, RoleRepository $roleRepository){
        if(RoleTrait::hasPermission(9)){
            return $roleRepository->deleteRole($role);
        }
    }
}
