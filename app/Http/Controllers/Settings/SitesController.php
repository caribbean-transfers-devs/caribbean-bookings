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

class SitesController extends Controller
{
    use RoleTrait;
    
    private $SitesRepository;

    public function __construct(SitesRepository $SitesRepository)
    {
        $this->SitesRepository = $SitesRepository;
    }

    public function index(Request $request, $id)
    {
        if($this->hasPermission(102)){
            return $this->SitesRepository->index($request, $id);
        }
    }

    public function create(Request $request, $id)
    {
        return $this->SitesRepository->create($request, $id);
    }

    public function store(SiteRequest $request, $id)
    {
        return $this->SitesRepository->store($request, $id);
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
