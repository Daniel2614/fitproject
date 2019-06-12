<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\Plan;
use App\VitalGym\Entities\Payment;
use App\VitalGym\Entities\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\VitalGym\Entities\Membership;
use App\Mail\MembershipOrderConfirmationEmail;
use App\Http\Requests\CreateMembershipFormRequest;
use App\Http\Requests\UpdateMembershipFormRequest;

class MembershipController extends Controller
{
    public function index()
    {
        $memberships = Membership::with('customer', 'plan')->filter(request())->orderByDesc('created_at')->paginate();

        return view('admin.memberships.index', compact('memberships'));
    }

    public function create($planId)
    {
        $plan = Plan::findOrFail($planId);
        $customers = Customer::all();

        return view('admin.memberships.create', compact('customers', 'plan'));
    }

    public function edit($membershipId)
    {
        $customers = Customer::all();
        $membership = Membership::with('customer', 'plan', 'payment')->findOrFail($membershipId);
        $customer = $membership->customer;
        $plan = $membership->plan;
        $payment = $membership->payment;

        return view('admin.memberships.edit', compact('membership', 'customers', 'customer', 'plan', 'payment'));
    }

    public function show($membershipId)
    {
        $membership = Membership::with('customer', 'plan', 'payment')->findOrFail($membershipId);
        $customer = $membership->customer;
        $plan = $membership->plan;
        $payment = $membership->payment;

        return view('admin.memberships.show', compact('membership', 'customer', 'plan', 'payment'));
    }

    public function store(CreateMembershipFormRequest $request, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $payment = Payment::create([
            'customer_id' => $request->get('customer_id'),
            'total_price' => $plan->price * $request->get('membership_quantity'),
            'membership_quantity' => $request->get('membership_quantity'),
            'user_id' => auth()->user()->id,
        ]);

        $membership = $payment->membership()->create([
            'date_start' => now()->parse($request->get('date_start')),
            'date_end' => now()->parse($request->get('date_end')),
            'total_days' => $request->get('total_days'),
            'plan_id' => $plan->id,
            'customer_id' => $payment->customer_id,
        ]);

        Mail::to($membership->customer->email)->send(new MembershipOrderConfirmationEmail($membership));

        return redirect()->route('admin.memberships.index')->with(['message' => 'Membresía guardada con éxito', 'alert-type' => 'success']);
    }

    public function update(UpdateMembershipFormRequest $request, $membershipId)
    {
        $membership = Membership::findOrFail($membershipId);

        $membership->update([
            'date_start' => now()->parse($request->get('date_start')),
            'date_end' => now()->parse($request->get('date_end')),
            'total_days' => $request->get('total_days'),
            'customer_id' => $request->get('customer_id'),
        ]);
        $membership->payment()->update([
            'customer_id' => $membership->customer_id,
            'total_price' => $membership->plan->price * $request->get('membership_quantity'),
            'membership_quantity' => $request->get('membership_quantity'),
            'user_id' => auth()->user()->id,
        ]);

        Mail::to($membership->customer->email)->send(new MembershipOrderConfirmationEmail($membership));

        return redirect()->route('admin.memberships.index')->with(['message' => 'Membresía actulizada con éxito', 'alert-type' => 'success']);
    }

    public function destroy($membershipId)
    {
        $membership = Membership::findorFail($membershipId);
        $membership->delete();
        $membership->payment()->delete();

        return redirect()->route('admin.memberships.index')->with(['message' => 'Membresía Borrada con éxito', 'alert-type' => 'success']);
    }
}
