<?php

namespace App\Repositories\Tpv;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiTrait;


class AutocompleteRepository
{
    use ApiTrait;

    public function autocomplete($keyword){
        
        $this->sendAutocomplete($keyword);
        die("END...");
    }
}