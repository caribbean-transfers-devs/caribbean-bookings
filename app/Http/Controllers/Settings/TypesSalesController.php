<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\TypesSalesRequest;

//REPOSITORY
use App\Repositories\Settings\TypesSalesRepository;

//TRAITS
use App\Traits\RoleTrait;

class TypesSalesController extends Controller
{
    use RoleTrait;

    private $TypesSalesRepository;

    public function __construct(TypesSalesRepository $TypesSalesRepository)
    {
        $this->TypesSalesRepository = $TypesSalesRepository;
    }    

    public function index(Request $request)
    {
        if(!$this->hasPermission(115)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesSalesRepository->index($request);
    }

    public function create(Request $request)
    {
        if(!$this->hasPermission(116)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesSalesRepository->create($request);
    }

    public function store(TypesSalesRequest $request)
    {
        if(!$this->hasPermission(116)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesSalesRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        if(!$this->hasPermission(117)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesSalesRepository->edit($request, $id);
    }

    public function update(TypesSalesRequest $request, $id)
    {
        if(!$this->hasPermission(117)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesSalesRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        if(!$this->hasPermission(118)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesSalesRepository->destroy($request, $id);
    }    
}
