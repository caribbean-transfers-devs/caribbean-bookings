<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Repositories\Users\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request, UserRepository $userRepository)
    {
        return $userRepository->indexUsers($request);
    }
}
