<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{   
    public function index(){
        return view('auth.login');
    }

    public function check(){
        die("Funcionalidad para validar el REQUEST");
    }
}