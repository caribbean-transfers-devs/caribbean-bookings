<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REPOSITORY
use App\Repositories\Settings\UserRepository;

//TRAITS
use App\Traits\RoleTrait;

//REQUEST
use App\Http\Requests\UserRequest;
use App\Http\Requests\ChgPassRequest;
use App\Http\Requests\ValidIPRequest;

//MODELS
use App\Models\User;
use App\Models\WhitelistIp;

class UserController extends Controller
{
    use RoleTrait;

    private $UserRepository;

    public function __construct(UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }

    public function index(Request $request)
    {
        $this->authorizeAction(1);
        return $this->UserRepository->indexUsers($request);
    }

    public function create(Request $request)
    {
        $this->authorizeAction(2);
        return $this->UserRepository->createUser($request);
    }

    public function edit(Request $request, User $user)
    {
        $this->authorizeAction(3);
        return $this->UserRepository->editUser($request, $user);
    }

    public function store(UserRequest $request)
    {
        $this->authorizeAction(2);
        return $this->UserRepository->storeUser($request);
    }

    public function update(UserRequest $request, User $user)
    {
        $this->authorizeAction(3);
        return $this->UserRepository->updateUser($request, $user);
    }

    public function change_pass(ChgPassRequest $request, User $user)
    {
        $this->authorizeAction(3);
        return $this->UserRepository->changePass($request, $user);
    }

    public function change_status(Request $request, User $user)
    {
        $this->authorizeAction(4);
        return $this->UserRepository->changeStatus($request, $user);
    }

    public function store_ips(ValidIPRequest $request)
    {
        $this->authorizeAction(5);
        return $this->UserRepository->storeIps($request);
    }

    public function delete_ips(Request $request, WhitelistIp $ip)
    {
        $this->authorizeAction(5);
        return $this->UserRepository->deleteIps($request, $ip);
    }

    /**
     * Verifica si el usuario tiene el permiso requerido.
     */
    private function authorizeAction(int $permissionId)
    {
        abort_unless($this->hasPermission2($permissionId), 403, 'NO TIENE AUTORIZACIÓN.');
    } 
}
