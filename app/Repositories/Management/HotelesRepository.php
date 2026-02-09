<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;

//FACADES
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

//TRAITS
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

//MODELS
use App\Models\Destination;
use App\Models\Autocomplete;

class HotelesRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        ini_set('memory_limit', '-1'); // Sin límite
        
        return view('management.hotels.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de hoteles",
                    "active" => true
                ]
            ],
            'destinations' => Destination::get(),
            'hotels' => Autocomplete::with(['zone.destination'])->get(),
        ]);
    }

    public function hotelAdd($request)
    {
        $validator = Validator::make($request->all(), [
            'destinationID' => 'required|integer|min:1|exists:destinations,id',
            'zoneId' => 'required|integer|min:1|exists:zones,id',
            'from_name' => 'required|string',
            'from_address' => 'required|string',
            'from_lat' => 'required|string',
            'from_lng' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => [
                    'code' => 'REQUIRED_PARAMS',
                    'message' =>  $validator->errors()->all()
                ],
                'status' => 'error',
                "message" => $validator->errors()->all(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY);  // 422
        }

        try {
            DB::beginTransaction();
            
            $autocomple = new Autocomplete();
            $autocomple->name = $request->from_name;
            $autocomple->address = $request->from_address;
            $autocomple->latitude = $request->from_lat;
            $autocomple->longitude = $request->from_lng;
            $autocomple->zone_id = $request->zoneId;
            $autocomple->save();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Se agrego correctamente el hotel',
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => [
                    'code' => 'INTERNAL_SERVER',
                    'message' =>  $e->getMessage()
                ],
                'status' => 'error',                
                'message' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete($request)
    {
        $hotel = Autocomplete::find($request->id);

        if(!$hotel) {
            return response()->json([
                'status' => 'error',
                "message" => 'No se encontró el hotel',
            ], Response::HTTP_NOT_FOUND);
        }
            
        try {
            $hotel->delete();
        } catch(Exception $e) {
            return response()->json([
                'status' => 'error',
                "message" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Se eliminó correctamente el hotel',
        ], Response::HTTP_OK);
    }
}