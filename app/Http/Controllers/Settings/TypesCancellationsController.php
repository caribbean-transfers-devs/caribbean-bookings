<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\TypesCancellationRequest;

//REPOSITORY
use App\Repositories\Settings\TypesCancellationsRepository;

//TRAITS
use App\Traits\RoleTrait;

class TypesCancellationsController extends Controller
{
    use RoleTrait;

    private $TypesCancellationsRepository;

    public function __construct(TypesCancellationsRepository $TypesCancellationsRepository)
    {
        $this->TypesCancellationsRepository = $TypesCancellationsRepository;
    }    

    public function index(Request $request)
    {
        if(!RoleTrait::hasPermission(108)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesCancellationsRepository->index($request);
    }

    public function create(Request $request)
    {
        if(!RoleTrait::hasPermission(109)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesCancellationsRepository->create($request);
    }

    public function store(TypesCancellationRequest $request)
    {
        if(!RoleTrait::hasPermission(109)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesCancellationsRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        if(!RoleTrait::hasPermission(110)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesCancellationsRepository->edit($request, $id);
    }

    public function update(TypesCancellationRequest $request, $id)
    {
        if(!RoleTrait::hasPermission(110)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesCancellationsRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        if(!RoleTrait::hasPermission(111)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->TypesCancellationsRepository->destroy($request, $id);
    }    
}
