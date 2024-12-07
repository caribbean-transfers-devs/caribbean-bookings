<?php

namespace App\Http\Controllers\Sites;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\SiteRequest;

//REPOSITORY
use App\Repositories\Sites\SitesRepository;

//MODEL
use App\Models\Enterprise;
use App\Models\Site;

class SitesController extends Controller
{
    private $SitesRepository;

    public function __construct(SitesRepository $SitesRepository)
    {
        $this->SitesRepository = $SitesRepository;
    }

    public function index(Enterprise $Enterprise)
    {
        return $this->SitesRepository->index($Enterprise);
    }    

    public function create(Request $request, Enterprise $Enterprise)
    {
        return $this->SitesRepository->create($request, $Enterprise);
    }

    public function store(SiteRequest $request, Enterprise $Enterprise)
    {
        return $this->SitesRepository->store($request, $Enterprise);
    }

    public function edit(Request $request, Site $Site)
    {
        return $this->SitesRepository->edit($request, $Site);
    }

    public function update(SiteRequest $request, Site $Site)
    {
        return $this->SitesRepository->update($request, $Site);
    }

    public function destroy(Request $request, Site $Site)
    {
        return $this->SitesRepository->destroy($request, $Site);
    }     
}
