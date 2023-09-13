<?php
namespace App\Http\Controllers\Tpv;

use App\Http\Controllers\Controller;
use App\Http\Requests\TpvRequest;
use App\Repositories\Tpv\TpvRepository;
use App\Repositories\Tpv\AutocompleteRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TpvController extends Controller
{   
    public function handler(Request $request, TpvRepository $tpvRepository){
        return $tpvRepository->handler($request);
    }

    public function index(Request $request, $code, TpvRepository $tpvRepository){     

        if (!$request->code) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'code is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        return $tpvRepository->index($request);
    }

    public function quote(Request $request, $code, TpvRepository $tpvRepository){
        return $tpvRepository->quote($request);
    }

    public function autocomplete(Request $request, $code, AutocompleteRepository $autocompleteRepository){

        if ( !$request->keyword || empty($request->keyword) ) {
            return response()->json([
                    'error' => [
                        'code' => 'required_params', 
                        'message' =>  'keyword is required'
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }


        return $autocompleteRepository->autocomplete($request->keyword);
    }
}