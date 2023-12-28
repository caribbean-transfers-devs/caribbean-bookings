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
}