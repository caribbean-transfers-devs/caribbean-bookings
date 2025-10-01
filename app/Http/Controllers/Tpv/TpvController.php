<?php
namespace App\Http\Controllers\Tpv;

use App\Http\Controllers\Controller;
use App\Http\Requests\TpvRequest;
use App\Http\Requests\TpvCreateRequest;
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

    public function quote(
        TpvRequest $request,
        TpvRepository $tpvRepository){
        return $tpvRepository->quote($request);
    }

    public function create(
        TpvCreateRequest $request,
        TpvRepository $tpvRepository) {
        return $tpvRepository->create($request);
    }

    public function autocomplete(Request $request, $code, AutocompleteRepository $autocompleteRepository){
        return $autocompleteRepository->autocomplete($request);
    }
}
