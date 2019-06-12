<?php

namespace App\Http\Controllers;

use App\VitalGym\Entities\Attendance;

class CustomerAttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::where('customer_id', auth()->user()->customer->id)
                           ->orderByDesc('created_at')
                           ->paginate();

        return view('customer.attendances.index', compact('attendances'));
    }
}
