<?php

namespace App\Http\Controllers;

use App\VitalGym\Entities\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();

        return view('plans.index', compact('plans'));
    }
}
