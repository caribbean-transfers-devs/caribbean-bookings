<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboards\DashboardRepository;
use App\Repositories\Operation\OperationRepository;
use App\Traits\RoleTrait;

class DashboardController extends Controller
{   
    use RoleTrait;

    private $DashboardRepository;
    private $ReportsRepository;    

    public function __construct(DashboardRepository $DashboardRepository, OperationRepository $OperationRepository)
    {
        $this->DashboardRepository = $DashboardRepository;
        $this->OperationRepository = $OperationRepository;
    }    

    public function index(Request $request){
        $roles = session()->get('roles');
        
        if( in_array(4, $roles['roles']) ){
            return $this->OperationRepository->reservations($request);
        }else{
            return $this->DashboardRepository->index($request);
        }
    }

    public function admin(Request $request){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        return $this->DashboardRepository->admin($request);        
    }
}