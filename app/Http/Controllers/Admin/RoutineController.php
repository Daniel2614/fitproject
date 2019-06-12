<?php

namespace App\Http\Controllers\Admin;

use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\Routine;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\CreateRoutineFormRequest;
use App\Http\Requests\UpdateRoutineFormRequest;

class RoutineController extends Controller
{
    public function index()
    {
        $routines = Routine::with('level')->withCount('customers')->orderByDesc('created_at')->paginate();

        return view('admin.routines.index', compact('routines'));
    }

    public function create()
    {
        $levels = Level::all();

        return view('admin.routines.create', compact('levels'));
    }

    public function store(CreateRoutineFormRequest $request)
    {
        Routine::create([
            'level_id' => $request->get('level_id'),
            'name' => $request->get('name'),
            'description' => $request->get('description'),
            'file' => $request->file('file')->store('files'),
        ]);

        return redirect()->route('admin.routines.index')->with(['message' => 'Rutina guardada con éxito', 'alert-type' => 'success']);
    }

    public function show($routineId)
    {
        $routine = Routine::with('level')->findOrFail($routineId);

        return view('admin.routines.show', compact('routine'));
    }

    public function edit($routineId)
    {
        $levels = Level::all();
        $routine = Routine::with('level')->findOrFail($routineId);

        return view('admin.routines.edit', compact('routine', 'levels'));
    }

    public function update(UpdateRoutineFormRequest $request, $routineId)
    {
        $routine = Routine::findOrFail($routineId);
        $routine->update($request->routineParams());

        return redirect()->route('admin.routines.index')->with(['message' => 'Rutina actualizada con éxito', 'alert-type' => 'success']);
    }

    public function destroy($routineId)
    {
        $routine = Routine::withCount('customers')->findOrFail($routineId);

        if ($routine->customers_count > 0) {
            return redirect()->route('admin.routines.index')->with(['message' => 'No se puede eliminar una rutina asociada a clientes', 'alert-type' => 'error']);
        }

        Storage::delete($routine->file);
        $routine->delete();

        return redirect()->route('admin.routines.index')->with(['message' => 'Rutina Eliminada con éxito', 'alert-type' => 'success']);
    }

    public function downloadFile($routineId)
    {
        $routine = Routine::findOrFail($routineId);

        if ($routine->file) {
            $extension = File::extension($routine->file);
            $fileName = str_slug($routine->name).'.'.$extension;

            return Storage::download($routine->file, $fileName);
        }

        return redirect()->back()->with(['message' => 'El archivo no se econtró', 'alert-type' => 'error']);
    }
}
