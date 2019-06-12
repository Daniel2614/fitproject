<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\Customer;
use App\Http\Controllers\Controller;
use App\VitalGym\Entities\Attendance;
use App\Http\Requests\CreateAttendanceFormRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('customer')->orderByDesc('id')->paginate();

        return view('admin.attendances.index', compact('attendances'));
    }

    public function create()
    {
        $customers = Customer::with('user')->orderByDesc('id')->paginate();

        return view('admin.attendances.create', compact('customers'));
    }

    public function store(CreateAttendanceFormRequest $request)
    {
        Attendance::create($request->validated());

        return redirect()->route('admin.attendances.index')
                         ->with(['alert-type' => 'success', 'message' => 'Asistencia Registrada con éxito']);
    }

    public function destroy($attendanceId)
    {
        $attendance = Attendance::findOrFail($attendanceId);
        $attendance->delete();

        return redirect()->route('admin.attendances.index')->with(['alert-type' => 'success', 'message' => 'Asistencia eliminada con éxito']);
    }
}
