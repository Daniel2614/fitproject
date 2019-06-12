<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\Plan;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('memberships')->orderByDesc('created_at')->paginate();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function edit($planId)
    {
        $plan = Plan::findOrFail($planId);

        return view('admin.plans.edit', compact('plan'));
    }

    public function store()
    {
        $validatedData = request()->validate([
            'name' => 'required|max:60',
            'price' => 'required|integer',
            'is_premium' => 'required|boolean',
        ]);

        Plan::create($validatedData);

        return redirect()->route('admin.plans.index')
                         ->with(['alert-type' => 'success', 'message' => 'Plan creado con éxito']);
    }

    public function update($planId)
    {
        $validatedData = request()->validate([
           'name' => 'required|max:60',
           'price' => 'required|integer',
           'is_premium' => 'required|boolean',
        ]);

        $plan = Plan::findOrfail($planId);

        $plan->update($validatedData);

        return redirect()->route('admin.plans.index')
                         ->with(['alert-type' => 'success', 'message' => 'Plan actualizado con éxito']);
    }

    public function destroy($planId)
    {
        $plan = Plan::withCount('memberships')->findOrFail($planId);

        if ($plan->memberships_count > 0) {
            return redirect()->route('admin.plans.index')
                             ->with(['alert-type' => 'error', 'message' => 'No se puede eliminar un plan asociado a membresías']);
        }
        $plan->delete();

        return redirect()->route('admin.plans.index')
                         ->with(['alert-type' => 'success', 'message' => 'Plan Eliminado con éxito']);
    }
}
