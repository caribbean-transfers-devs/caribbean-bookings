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

class UserController extends Controller
{
    public function index(Request $request, UserRepository $userRepository)
    {
        return $userRepository->indexUsers($request);
    }

    public function create(Request $request, UserRepository $userRepository)
    {
        return $userRepository->createUser($request);
    }

    public function edit(Request $request, User $user, UserRepository $userRepository)
    {
        return $userRepository->editUser($request, $user);
    }

    public function store(UserRequest $request, UserRepository $userRepository)
    {
        return $userRepository->storeUser($request);
    }

    public function update(UserRequest $request, User $user, UserRepository $userRepository)
    {
        return $userRepository->updateUser($request, $user);
    }

    public function change_pass(ChgPassRequest $request, User $user, UserRepository $userRepository)
    {
        return $userRepository->changePass($request, $user);
    }

    public function change_status(Request $request, User $user, UserRepository $userRepository)
    {
        return $userRepository->changeStatus($request, $user);
    }

    public function store_ips(ValidIPRequest $request, UserRepository $userRepository)
    {
        return $userRepository->storeIps($request);
    }

    public function delete_ips(Request $request, WhitelistIp $ip, UserRepository $userRepository)
    {
        return $userRepository->deleteIps($request, $ip);
    }
}
