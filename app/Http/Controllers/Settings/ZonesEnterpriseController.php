<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

//REQUEST
use App\Http\Requests\ZoneRequest;

//REPOSITORY
use App\Repositories\Settings\ZonesEnterpriseRepository;

//TRAITS
use App\Traits\RoleTrait;

//MODELS

class ZonesEnterpriseController extends Controller
{ 
    use RoleTrait;
    
    private $zone;

    public function __construct(ZonesEnterpriseRepository $ZonesEnterpriseRepository)
    {
        $this->zone = $ZonesEnterpriseRepository;
    }

    public function index(Request $request, $id)
    {
        return $this->zone->index($request, $id);
    }

    public function create(Request $request, $id)
    {
        return $this->zone->create($request, $id);
    }

    public function store(ZoneRequest $request, $id)
    {
        return $this->zone->store($request, $id);
    }

    public function edit(Request $request, $id)
    {
        return $this->zone->edit($request, $id);
    }

    public function update(ZoneRequest $request, $id)
    {
        return $this->zone->update($request, $id);
    }

    public function destroy(Request $request, $id)
    {
        return $this->zone->destroy($request, $id);
    }

    public function getPoints(Request $request){
        if (!$request->id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'destination_id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        if (!$request->zone_id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'zone_id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        return $this->zone->points($request);
    }

    public function setPoints(Request $request){
        if (!$request->id) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'id is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }
        return $this->zone->setpoints($request);
    }
}