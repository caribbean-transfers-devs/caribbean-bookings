<?php

namespace App\Repositories\Finance;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

//MODELS
use App\Models\Payment;

//TRAITS
use App\Traits\FiltersTrait;
use App\Traits\QueryTrait;
use App\Traits\PayPalTrait;
use App\Traits\StripeTrait;

class StripeRepository
{
    use FiltersTrait;
    
}