<?php

namespace App\Repositories\Management;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

//TRAIT
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;

class AfterSalesRepository
{
    use FiltersTrait, QueryTrait;

    public function index($request)
    {
        return view('management.aftersales.index', [
            'breadcrumbs' => [
                [
                    "route" => "",
                    "name" => "Gestion de post venta",
                    "active" => true
                ]
            ],            
        ]);
    }
}