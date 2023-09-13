<?php

namespace App\Http\Controllers\Payments;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentRequest;
use App\Models\Payment;
use App\Repositories\Payments\PaymentRepository;

class PaymentsController extends Controller
{
    public function store(PaymentRequest $request, PaymentRepository $paymentRepository)
    {
        return $paymentRepository->store($request);
    }

    public function show(Payment $payment)
    {
        return $payment;
    }

    public function update(PaymentRequest $request, PaymentRepository $paymentRepository,Payment $payment)
    {
        return $paymentRepository->update($request,$payment);
    }

    public function destroy(Request $request, PaymentRepository $paymentRepository,Payment $payment)
    {
        return $paymentRepository->destroy($request,$payment);
    }
}
