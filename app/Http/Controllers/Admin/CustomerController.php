<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Level;
use App\Mail\CustomerWelcomeEmail;
use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\CreateCustomerFormRequest;
use App\Http\Requests\UpdateCustomerFormRequest;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('user', 'level')->filter(request())->orderByDesc('id')->paginate();

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $levels = Level::all();
        $routines = Routine::all();

        return view('admin.customers.create', compact('levels', 'routines'));
    }

    public function edit($customerId)
    {
        $customer = Customer::with('routine', 'level')->findOrFail($customerId);
        $routine = $customer->routine;
        $level = $customer->level;
        $levels = Level::all();
        $routines = Routine::all();

        return view('admin.customers.edit', compact('customer', 'level', 'routine', 'levels', 'routines'));
    }

    public function show($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        return view('admin.customers.show', compact('customer'));
    }

    public function store(CreateCustomerFormRequest $request)
    {
        $user = User::createWithActivationToken($request->userRequestParams());
        $user->customer()->create($request->customerRequestParams());
        Mail::to($user->email)->send(new CustomerWelcomeEmail($user->customer));

        return redirect()->route('admin.customers.index')
                         ->with(['alert-type' => 'success', 'message' => 'cliente creado con éxito']);
    }

    public function update(UpdateCustomerFormRequest $request, $customerId)
    {
        $customer = Customer::with('user')->findOrFail($customerId);

        $customer->update($request->customerRequestParams());
        $customer->user->update($request->userRequestParams());

        return redirect()->route('admin.customers.index')
                         ->with(['alert-type' => 'success', 'message' => 'cliente actualizado con éxito']);
    }

    public function destroy($customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $customer->delete();

        return redirect()->route('admin.customers.index')->with(['alert-type' => 'success', 'message' => 'Cliente eliminado con éxito']);
    }
}
