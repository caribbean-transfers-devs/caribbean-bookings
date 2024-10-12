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
        $roles = session()->get('roles');
        // if( ( !RoleTrait::hasPermission(42)) || ( !RoleTrait::hasPermission(62)) || ( !RoleTrait::hasPermission(63)) ){
        //     abort(403, 'NO TIENE AUTORIZACIÓN.');
        // }
        return $dashboard->index($dashboard);   
    }

    public function admin(DashboardRepository $dashboard){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $dashboard->admin();        
    }
}