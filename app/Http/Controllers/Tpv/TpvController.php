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
                        'message' =>  $validator->errors()->all() 
                    ]
                ], Response::HTTP_BAD_REQUEST);
        }

        return $tpvRepository->index($request);
    }

    /*public function detail(Request $request, AutocompleteRepository $autocompleteRepository)
    {
        return $autocompleteRepository->search($request);
    }*/
}