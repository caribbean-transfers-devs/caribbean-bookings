<?php
namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Dashboards\DashboardRepository;
use App\Repositories\Dashboards\CallCenterResository;
use App\Repositories\Operation\OperationRepository;
use App\Traits\RoleTrait;

class DashboardController extends Controller
{   
    use RoleTrait;

    private $DashboardRepository;
    private $CallCenterResository;
    private $OperationRepository;

    public function __construct(DashboardRepository $DashboardRepository, CallCenterResository $CallCenterResository, OperationRepository $OperationRepository)
    {
        $this->DashboardRepository = $DashboardRepository;
        $this->CallCenterResository = $CallCenterResository;
        $this->OperationRepository = $OperationRepository;
    }    

    public function index(Request $request){
        return $this->DashboardRepository->index($request);
    }

    public function admin(Request $request){
        if(!RoleTrait::hasPermission(42)){
            //abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->DashboardRepository->admin($request);        
    }
}