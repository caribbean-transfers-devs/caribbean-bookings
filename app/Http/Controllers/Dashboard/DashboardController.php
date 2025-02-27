<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboards\DashboardRepository;
use App\Repositories\Dashboards\CallCenterResository;
use App\Repositories\Operation\OperationRepository;
use App\Repositories\Management\ManagementRepository;
use App\Traits\RoleTrait;

class DashboardController extends Controller
{   
    use RoleTrait;

    private $DashboardRepository;
    private $CallCenterResository;
    private $OperationRepository;
    private $ManagementRepository;

    public function __construct(DashboardRepository $DashboardRepository, CallCenterResository $CallCenterResository, OperationRepository $OperationRepository, ManagementRepository $ManagementRepository)
    {
        $this->DashboardRepository = $DashboardRepository;
        $this->CallCenterResository = $CallCenterResository;
        $this->OperationRepository = $OperationRepository;
        $this->ManagementRepository = $ManagementRepository;
    }    

    public function index(Request $request){
        $roles = session()->get('roles');
        $dataUser = auth()->user();
        // in_array(3, $roles['roles']) || in_array(4, $roles['roles']);
        if( RoleTrait::hasPermission(113) && $dataUser->is_commission == 1 ){
            return $this->CallCenterResository->index($request);
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