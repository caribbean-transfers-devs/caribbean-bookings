<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\SiteRequest;

//REPOSITORY
use App\Repositories\Sites\SitesRepository;

class SitesController extends Controller
{
    private $SitesRepository;

    public function __construct(SitesRepository $SitesRepository)
    {
        $this->SitesRepository = $SitesRepository;
    }

    public function index(Request $request)
    {
        return $this->SitesRepository->index($request);
    }

    public function create(Request $request)
    {
        return $this->SitesRepository->create($request);
    }

    public function store(SiteRequest $request)
    {
        return $this->SitesRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        return $this->SitesRepository->edit($request, $id);
    }

    public function update(SiteRequest $request, $id)
    {
        return $this->SitesRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->SitesRepository->destroy($request, $id);
    }     
}
