<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//REQUEST
use App\Http\Requests\ScheduleRestrictionRequest;

//REPOSITORY
use App\Repositories\Settings\ScheduleRestrictionsRepository;

//TRAITS
use App\Traits\RoleTrait;

class ScheduleRestrictionsController extends Controller
{
    use RoleTrait;

    private $ScheduleRestrictionsRepository;

    public function __construct(ScheduleRestrictionsRepository $ScheduleRestrictionsRepository)
    {
        $this->ScheduleRestrictionsRepository = $ScheduleRestrictionsRepository;
    }

    public function index(Request $request)
    {
        if(!$this->hasPermission(135)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ScheduleRestrictionsRepository->index($request);
    }

    public function create(Request $request)
    {
        if(!$this->hasPermission(136)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ScheduleRestrictionsRepository->create($request);
    }

    public function store(ScheduleRestrictionRequest $request)
    {
        if(!$this->hasPermission(136)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ScheduleRestrictionsRepository->store($request);
    }

    public function edit(Request $request, $id)
    {
        if(!$this->hasPermission(137)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ScheduleRestrictionsRepository->edit($request, $id);
    }

    public function update(ScheduleRestrictionRequest $request, $id)
    {
        if(!$this->hasPermission(137)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ScheduleRestrictionsRepository->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        if(!$this->hasPermission(138)){
            abort(403, 'NO TIENE AUTORIZACIÓN.');
        }
        return $this->ScheduleRestrictionsRepository->destroy($request, $id);
    }
}
