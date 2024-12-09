<?php

namespace App\Http\Controllers\Configs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\ExchangeRequest;

//REPOSITORY
use App\Repositories\Exchange\ExchangeReportsRepository;

//TRAITS
use App\Traits\RoleTrait;

class ExchangeReportsController extends Controller
{
    use RoleTrait;
    private $ExchangeReportsRepository;

    public function __construct(ExchangeReportsRepository $ExchangeReportsRepository)
    {
        $this->ExchangeReportsRepository = $ExchangeReportsRepository;
    }

    public function index(Request $request)
    {
        return $this->ExchangeReportsRepository->index($request);
    }

    public function create(Request $request)
    {
        return $this->ExchangeReportsRepository->create($request);
    }

    public function store(ExchangeRequest $request)
    {
        return $this->ExchangeReportsRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->ExchangeReportsRepository->edit($request, $id);
    }

    public function update(ExchangeRequest $request, $id)
    {
        return $this->ExchangeReportsRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->ExchangeReportsRepository->destroy($request, $id);
    }
}
