<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use App\Repositories\Sales\SaleRepository;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function store(SaleRequest $request, SaleRepository $saleRepository)
    {
        return $saleRepository->store($request);
    }

    public function show(Sale $sale)
    {
        return $sale;
    }

    public function update(SaleRequest $request, SaleRepository $saleRepository,Sale $sale)
    {
        return $saleRepository->update($request,$sale);
    }

    public function destroy(Request $request, SaleRepository $saleRepository,Sale $sale)
    {
        return $saleRepository->destroy($request,$sale);
    }
}
