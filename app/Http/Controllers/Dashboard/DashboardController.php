<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboards\DashboardRepository;
use App\Traits\RoleTrait;

class DashboardController extends Controller
{   
    use RoleTrait;

    public function index(DashboardRepository $dashboard){
        return $dashboard->index();        
    }

    public function admin(DashboardRepository $dashboard){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $dashboard->admin();        
    }

    public function sales(Request $request, DashboardRepository $dashboard){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $dashboard->sales($request);
    }    
    
    public function online(Request $request, DashboardRepository $dashboard){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $dashboard->online($request);
    }
    
    public function airport(Request $request, DashboardRepository $dashboard){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $dashboard->airport($request);
    }
}