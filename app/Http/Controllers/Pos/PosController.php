<?php
namespace App\Http\Controllers\Pos;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Pos\PosRepository;
use App\Traits\RoleTrait;
use App\Http\Requests\PosCreateRequest;

class PosController extends Controller
{
    use RoleTrait;

    public function index(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(51) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->index($request);
    }

    public function generals(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(51) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->generals($request);
    }    

    public function detail(Request $request, PosRepository $posRepository, $id){
        if( !RoleTrait::hasPermission(53) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->detail($request, $id);
    }

    public function capture(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(52) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->capture($request);
    }

    public function create(PosCreateRequest $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(52) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->create($request);
    }

    public function update(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(77) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->update($request);
    }

    public function vendors(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(54) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->vendors($request);
    }

    public function createVendor(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(57) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->createVendor($request);
    }

    public function editVendor(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(55) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->editVendor($request);
    }

    public function deleteVendor(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(56) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->deleteVendor($request);
    }

    public function editCreatedAt(Request $request, PosRepository $posRepository){
        if( !RoleTrait::hasPermission(59) ) abort(403, 'NO TIENE AUTORIZACIÓN.');

        return $posRepository->editCreatedAt($request);
    }

}