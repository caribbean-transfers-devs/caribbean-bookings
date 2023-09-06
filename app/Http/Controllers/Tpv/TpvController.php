<?php
namespace App\Http\Controllers\Tpv;

use App\Http\Controllers\Controller;
use App\Repositories\Tpv\TpvRepository;
use App\Repositories\Tpv\AutocompleteRepository;
use Illuminate\Http\Request;

class TpvController extends Controller
{
    public function index(Request $request, TpvRepository $tpvRepository){
        return $tpvRepository->index($request);
    }

    public function detail(Request $request, AutocompleteRepository $autocompleteRepository)
    {
        return $autocompleteRepository->search($request);
    }
}