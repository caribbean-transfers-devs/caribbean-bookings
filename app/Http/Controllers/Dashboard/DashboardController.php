<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboards\DashboardRepository;

class DashboardController extends Controller
{   
    public function index(DashboardRepository $dashboard){
        return $dashboard->index();        
    }
    public function admin(DashboardRepository $dashboard){
        return $dashboard->admin();        
    }
}