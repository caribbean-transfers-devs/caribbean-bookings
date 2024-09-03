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

    public function sales(Request $request, $type, DashboardRepository $dashboard){
        $roles = session()->get('roles');
        // dd($roles);
        if(  ( $type == "general" && !RoleTrait::hasPermission(42)) || ( $type == "online" && !RoleTrait::hasPermission(62)) || ( $type == "airport" && !RoleTrait::hasPermission(63))  ){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }            
        return $dashboard->sales($request, $type);
    }

    public function sales2(Request $request, $type, DashboardRepository $dashboard){
        $roles = session()->get('roles');
        // dd($roles);
        if(  ( $type == "general" && !RoleTrait::hasPermission(42)) || ( $type == "online" && !RoleTrait::hasPermission(62)) || ( $type == "airport" && !RoleTrait::hasPermission(63))  ){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $dashboard->sales2($request, $type);
    }    
}