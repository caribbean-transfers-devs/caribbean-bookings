<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Repositories\Roles\RoleRepository;

class RoleController extends Controller
{
    public function index(Request $request, RoleRepository $roleRepository){
        return $roleRepository->indexRoles($request);
    }

    public function create(Request $request,RoleRepository $roleRepository){
        return $roleRepository->createRole($request);
    }

    public function edit(Request $request, Role $role, RoleRepository $roleRepository){
        return $roleRepository->editRole($request,$role);
    }

    public function store(RoleRequest $request, RoleRepository $roleRepository){
        return $roleRepository->storeRole($request);
    }

    public function update(RoleRequest $request, Role $role, RoleRepository $roleRepository){
        return $roleRepository->updateRole($request,$role);
    }

    public function destroy(Role $role, RoleRepository $roleRepository){
        return $roleRepository->deleteRole($role);
    }
}
