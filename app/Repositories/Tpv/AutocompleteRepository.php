<?php

namespace App\Repositories\Tpv;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;


class AutocompleteRepository
{
    use ApiTrait;

    public function autocomplete($request){    
        if ( (!$request->keyword || empty($request->keyword)) || (!$request->uuid || empty($request->uuid)) ) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'keyword and uuid is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        
        $data = $this->sendAutocomplete($request->keyword, $request->uuid);
        if(isset($data['error'])):
            return response()->json([
                'error' => [
                    'code' => $data['error']['code'],
                    'message' => $data['error']['message'],
                ]
            ], 422);
        endif;
       
        return response()->json($data['items'], 200);
    }
}