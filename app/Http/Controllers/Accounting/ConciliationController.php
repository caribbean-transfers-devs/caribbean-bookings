<?php

namespace App\Http\Controllers\Accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

//REPOSITORY
use App\Repositories\Accounting\ConciliationRepository;

//TRAITS
use App\Traits\RoleTrait;

class ConciliationController extends Controller
{
    use RoleTrait;    

    private $ConciliationRepository;    

    public function __construct(ConciliationRepository $ConciliationRepository)
    {
        $this->ConciliationRepository = $ConciliationRepository;
    }

    public function PayPalPayments(Request $request)
    {
        return $this->ConciliationRepository->PayPalPayments($request);
    }
}
