<?php
namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\ReportsRepository;

//TRAIT
use App\Traits\RoleTrait;

class ReportsController extends Controller
{
    use RoleTrait;

    private $ReportsRepository;

    public function __construct(ReportsRepository $ReportsRepository)
    {
        $this->ReportsRepository = $ReportsRepository;
    }    

    public function operations(Request $request){
        return $this->ReportsRepository->operations($request);
    }

}