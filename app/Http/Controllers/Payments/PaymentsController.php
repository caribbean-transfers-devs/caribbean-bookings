<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Repositories\Payments\PaymentRepository;
use App\Traits\RoleTrait;

class PaymentsController extends Controller
{
    public function store(PaymentRequest $request, PaymentRepository $paymentRepository)
    {
        if(RoleTrait::hasPermission(14)){
            return $paymentRepository->store($request);
        }        
    }

    public function show(Payment $payment)
    {
        return $payment;
    }

    public function update(PaymentRequest $request, PaymentRepository $paymentRepository,Payment $payment)
    {
        if(RoleTrait::hasPermission(15)){
            return $paymentRepository->update($request,$payment);
        }
    }

    public function destroy(Request $request, PaymentRepository $paymentRepository,Payment $payment)
    {
        if(RoleTrait::hasPermission(16)){
            return $paymentRepository->destroy($request,$payment);
        }
    }
}
