<?php

namespace App\Repositories\Users;

use App\Models\User;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    public function indexUsers($request)
    {
        try {
            
            $active_users = User::where('status', 1)->get();
            $inactive_users = User::where('status', 0)->get();

            return view('users.index', compact('active_users', 'inactive_users'));
            
        } catch (\Throwable $e) {

            $active_users = [];
            $inactive_users = [];

            return view('users.index', compact('active_users', 'inactive_users'));
        }
    }
    
}
