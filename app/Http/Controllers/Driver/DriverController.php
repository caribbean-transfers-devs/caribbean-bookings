<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\DriverRequest;

//REPOSITORY
use App\Repositories\Driver\DriverRepository;

use App\Traits\RoleTrait;

class DriverController extends Controller
{
    private $DriverRepository;

    public function __construct(DriverRepository $DriverRepository)
    {
        $this->DriverRepository = $DriverRepository;
    }

    public function index(Request $request)
    {
        return $this->DriverRepository->index($request);
    }

    public function create(Request $request)
    {
        return $this->DriverRepository->create($request);
    }

    public function store(DriverRequest $request)
    {
        return $this->DriverRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->DriverRepository->edit($request, $id);
    }

    public function update(DriverRequest $request, $id)
    {
        return $this->DriverRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->DriverRepository->destroy($request, $id);
    }
}
