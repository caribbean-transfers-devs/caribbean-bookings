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
    use RoleTrait;

    private $RoleRepository;

    public function __construct(RoleRepository $RoleRepository)
    {
        $this->RoleRepository = $RoleRepository;
    }    

    public function index(Request $request){
        $this->authorizeAction(6);
        return $this->RoleRepository->indexRoles($request);
    }

    public function create(Request $request){
        $this->authorizeAction(7);
        return $this->RoleRepository->createRole($request);          
    }

    public function edit(Request $request, Role $role){
        $this->authorizeAction(8);
        return $this->RoleRepository->editRole($request,$role);  
    }

    public function store(RoleRequest $request){
        $this->authorizeAction(7);
        return $this->RoleRepository->storeRole($request);
    }

    public function update(RoleRequest $request, Role $role){
        $this->authorizeAction(8);
        return $this->RoleRepository->updateRole($request,$role);
    }

    public function destroy(Role $role){
        $this->authorizeAction(9);
        return $this->RoleRepository->deleteRole($role);
    }

    /**
     * Verifica si el usuario tiene el permiso requerido.
     */
    private function authorizeAction(int $permissionId)
    {
        abort_unless($this->hasPermission($permissionId), 403, 'NO TIENE AUTORIZACIÓN.');
    }    
}
