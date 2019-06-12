<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\Payment;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with('customer', 'user', 'membership.plan')->orderByDesc('created_at')->paginate();

        return view('admin.payments.index', compact('payments'));
    }
}
