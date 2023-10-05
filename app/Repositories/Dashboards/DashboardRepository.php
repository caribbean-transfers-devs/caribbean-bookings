<?php

namespace App\Repositories\Dashboards;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function index(){
        return view('dashboard.default');
    }

    public function admin(){
        return view('dashboard.admin');
    }
}