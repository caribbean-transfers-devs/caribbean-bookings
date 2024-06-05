<?php

namespace App\Http\Controllers\Vehicle;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\VehicleRequest;

//REPOSITORY
use App\Repositories\Vehicle\VehicleRepository;

class VehicleController extends Controller
{
    private $VehicleRepository;

    public function __construct(VehicleRepository $VehicleRepository)
    {
        $this->VehicleRepository = $VehicleRepository;
    }

    public function index(Request $request)
    {
        return $this->VehicleRepository->index($request);
    }

    public function create(Request $request)
    {
        return $this->VehicleRepository->create($request);
    }

    public function store(VehicleRequest $request)
    {
        return $this->VehicleRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->VehicleRepository->edit($request, $id);
    }

    public function update(VehicleRequest $request, $id)
    {
        return $this->VehicleRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->VehicleRepository->destroy($request, $id);
    } 
}
