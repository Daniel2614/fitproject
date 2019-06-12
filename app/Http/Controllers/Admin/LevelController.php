<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\VitalGym\Entities\Level;
use App\Http\Controllers\Controller;

class LevelController extends Controller
{
    public function index()
    {
        $levels = Level::withCount('customers', 'routines')->orderByDesc('created_at')->paginate();

        return view('admin.levels.index', compact('levels'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.levels.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|unique:levels|min:3|max:12',
        ]);

        $level = Level::create([
            'name' => $request->get('name'),
        ]);

        return redirect()->route('admin.levels.index')->with(['message' => 'Nivel guardado con éxito', 'alert-type' => 'success']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $level = Level::findOrFail($id);

        return view('admin.levels.show', compact('level'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $level = Level::findOrFail($id);

        return view('admin.levels.edit', compact('level'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);

        $level = Level::findOrFail($id);

        $level->update([
            'name' => $request->get('name'),
        ]);

        return redirect()->route('admin.levels.index')->with(['message' => 'Nivel actualizado con éxito', 'alert-type' => 'success']);
    }

    public function destroy($id)
    {
        $level = Level::withCount('customers', 'routines')->findorFail($id);

        if ($level->customers_count > 0) {
            return redirect()->back()->with(['message' => 'No se puede borrar un nivel que tiene clientes asociados', 'alert-type' => 'error']);
        }

        if ($level->routines_count > 0) {
            return redirect()->back()->with(['message' => 'No se puede borrar un nivel que tiene rutinas asociadas', 'alert-type' => 'error']);
        }

        $level->delete();

        return redirect()->back()->with(['message' => 'Nivel Borrado con éxito', 'alert-type' => 'success']);
    }
}
