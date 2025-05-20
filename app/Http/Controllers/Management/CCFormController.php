<?php
namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Management\CCFormRepository;

//TRAIT
use App\Traits\RoleTrait;

class CCFormController extends Controller
{
    use RoleTrait;

    private $CCFormRepository;

    public function __construct(CCFormRepository $CCFormRepository)
    {
        $this->CCFormRepository = $CCFormRepository;
    }

    public function index(Request $request){
        if(!$this->hasPermission(126)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->CCFormRepository->index($request);        
    }

    public function createPDF(Request $request){
        if(!$this->hasPermission(126)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->CCFormRepository->createPDF($request);        
    }
    
}