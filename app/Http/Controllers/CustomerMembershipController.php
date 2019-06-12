<?php

namespace App\Http\Controllers;

use App\VitalGym\Entities\Membership;

class CustomerMembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with('customer', 'plan')
                                 ->where('customer_id', auth()->user()->customer->id)
                                 ->orderByDesc('created_at')
                                 ->paginate();

        return view('customer.memberships.index', compact('memberships'));
    }
}
