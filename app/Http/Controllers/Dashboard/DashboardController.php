<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{   
    public function index(){
        //Aquí debemos verificar cual es el Dashboard principal asignado al usuario y lo mostramos...
        return view('dashboard.default');
    }
}