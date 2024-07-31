<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use App\Traits\RoleTrait;

class LoginController extends Controller
{   
    public function index(){
        return view('auth.login');
    }

    public function check(LoginRequest $request){
        if(!Auth::check())
        {
            $request->authenticate();
            $request->session()->regenerate();
            session(['roles' => RoleTrait::getRolesAndSubmodules()]);
            return redirect()->intended('dashboard.admin');
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerate();

        return redirect(url('/'));
    }
}