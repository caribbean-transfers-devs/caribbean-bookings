<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\SiteRequest;

//REPOSITORY
use App\Repositories\Settings\SitesRepository;

//TRAITS
use App\Traits\RoleTrait;

//MODELS
use App\Models\Site;

class SitesController extends Controller
{
    use RoleTrait;
    
    private $SitesRepository;

    public function __construct(SitesRepository $SitesRepository)
    {
        $this->SitesRepository = $SitesRepository;
    }

    public function index(Request $request)
    {
        if($this->hasPermission(102)){
            return $this->SitesRepository->index($request);
        }
    }

    public function create(Request $request)
    {
        return $this->SitesRepository->create($request);
    }

    public function store(SiteRequest $request)
    {
        return $this->SitesRepository->store($request);
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
