<?php

namespace App\Http\Controllers\Enterprise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\EnterpriseRequest;

//REPOSITORY
use App\Repositories\Enterprise\EnterpriseRepository;

class EnterpriseController extends Controller
{
    private $EnterpriseRepository;

    public function __construct(EnterpriseRepository $EnterpriseRepository)
    {
        $this->EnterpriseRepository = $EnterpriseRepository;
    }

    public function index(Request $request)
    {
        return $this->EnterpriseRepository->index($request);
    }

    public function create(Request $request)
    {
        return $this->EnterpriseRepository->create($request);
    }

    public function store(EnterpriseRequest $request)
    {
        return $this->EnterpriseRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->EnterpriseRepository->edit($request, $id);
    }

    public function update(EnterpriseRequest $request, $id)
    {
        return $this->EnterpriseRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->EnterpriseRepository->destroy($request, $id);
    }    
}
