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
    private $UserRepository;

    public function __construct(UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
    }

    public function index(Request $request)
    {
        if(RoleTrait::hasPermission(1)){
            return $this->UserRepository->indexUsers($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function create(Request $request)
    {
        if(RoleTrait::hasPermission(2)){
            return $this->UserRepository->createUser($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function edit(Request $request, User $user)
    {
        if(RoleTrait::hasPermission(3)){
            return $this->UserRepository->editUser($request, $user);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function store(UserRequest $request)
    {
        if(RoleTrait::hasPermission(2)){
            return $this->UserRepository->storeUser($request);
        }
    }

    public function update(UserRequest $request, User $user)
    {
        if(RoleTrait::hasPermission(3)){
            return $this->UserRepository->updateUser($request, $user);
        }
    }

    public function change_pass(ChgPassRequest $request, User $user)
    {
        if(RoleTrait::hasPermission(3)){
            return $this->UserRepository->changePass($request, $user);
        }
    }

    public function change_status(Request $request, User $user)
    {
        if(RoleTrait::hasPermission(4)){
            return $this->UserRepository->changeStatus($request, $user);
        }       
    }

    public function store_ips(ValidIPRequest $request)
    {
        if(RoleTrait::hasPermission(5)){
            return $this->UserRepository->storeIps($request);
        }        
    }

    public function delete_ips(Request $request, WhitelistIp $ip)
    {
        if(RoleTrait::hasPermission(5)){
            return $this->UserRepository->deleteIps($request, $ip);
        }
    }
}
