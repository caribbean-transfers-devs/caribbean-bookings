<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Repositories\Management\QuotationRepository;

//TRIT
use App\Traits\RoleTrait;

class QuotationController extends Controller
{
    use RoleTrait;
    
    public function get(Request $request, QuotationRepository $quotation){
        return $quotation->get($request);
    }
}
