<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChgPassRequest;
use App\Http\Requests\UserRequest;
use App\Http\Requests\ValidIPRequest;
use App\Models\User;
use App\Models\WhitelistIp;
use App\Repositories\Users\UserRepository;
use Illuminate\Http\Request;
use App\Traits\RoleTrait;

class UserController extends Controller
{
    public function index(Request $request, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(1)){
            return $userRepository->indexUsers($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function create(Request $request, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(2)){
            return $userRepository->createUser($request);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function edit(Request $request, User $user, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(3)){
            return $userRepository->editUser($request, $user);
        }else{
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
    }

    public function store(UserRequest $request, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(2)){
            return $userRepository->storeUser($request);
        }
    }

    public function update(UserRequest $request, User $user, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(3)){
            return $userRepository->updateUser($request, $user);
        }
    }

    public function change_pass(ChgPassRequest $request, User $user, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(3)){
            return $userRepository->changePass($request, $user);
        }
    }

    public function change_status(Request $request, User $user, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(4)){
            return $userRepository->changeStatus($request, $user);
        }       
    }

    public function store_ips(ValidIPRequest $request, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(5)){
            return $userRepository->storeIps($request);
        }        
    }

    public function delete_ips(Request $request, WhitelistIp $ip, UserRepository $userRepository)
    {
        if(RoleTrait::hasPermission(5)){
            return $userRepository->deleteIps($request, $ip);
        }
    }
}
