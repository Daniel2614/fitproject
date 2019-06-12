<?php

namespace App\Http\Controllers;

use App\VitalGym\Entities\Payment;

class CustomerPaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::where('customer_id', auth()->user()->customer->id)
                                 ->orderByDesc('created_at')
                                 ->paginate();

        return view('customer.payments.index', compact('payments'));
    }
}
