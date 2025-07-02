<?php

namespace App\Http\Controllers\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Reports\SalesRepository;

//TRAIT
use App\Traits\RoleTrait;

class SalesController extends Controller
{
    use RoleTrait;

    private $SalesRepository;    

    public function __construct(SalesRepository $SalesRepository)
    {
        $this->SalesRepository = $SalesRepository;
    }

    public function index(Request $request)
    {
        if(!$this->hasPermission(98)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }

        // Obtener el último segmento de la URL (en este caso, "cancun")
        $destination = $request->segment(count($request->segments()));

        // O también puedes usar la función helper de Laravel:
        $destination = last(request()->segments());

        if ($destination == 'cancun') {
            $id = 1;
        } else {
            $id = 2;
        }        

        return $this->SalesRepository->index($request, $id);
    }
}
