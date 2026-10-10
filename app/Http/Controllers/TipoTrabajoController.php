<?php

namespace App\Http\Controllers;

use App\Models\TipoTrabajo;
use Illuminate\Http\Request;

class TipoTrabajoController extends Controller
{
    public function index()
    {
        $tipos = TipoTrabajo::all();
        return view('admin.tipo_trabajos.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipo_trabajos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipo_trabajos,nombre',
        ]);

        TipoTrabajo::create($request->all());

        return redirect()->route('admin.tipo-trabajos.index')
            ->with('mensaje', 'Tipo de trabajo creado exitosamente.')
            ->with('icon', 'success');
    }

    public function edit(TipoTrabajo $tipoTrabajo)
    {
        return view('admin.tipo_trabajos.edit', compact('tipoTrabajo'));
    }

    public function update(Request $request, TipoTrabajo $tipoTrabajo)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:tipo_trabajos,nombre,' . $tipoTrabajo->id,
        ]);

        $tipoTrabajo->update($request->all());

        return redirect()->route('admin.tipo-trabajos.index')
            ->with('mensaje', 'Tipo de trabajo actualizado exitosamente.')
            ->with('icon', 'success');
    }

    public function destroy(TipoTrabajo $tipoTrabajo)
    {
        $tipoTrabajo->delete();

        return redirect()->route('admin.tipo-trabajos.index')
            ->with('mensaje', 'Tipo de trabajo enviado a la papelera.')
            ->with('icon', 'success');
    }

    public function trash()
    {
        $tipos = TipoTrabajo::onlyTrashed()->get();
        return view('admin.tipo_trabajos.trash', compact('tipos'));
    }

    public function restore(int $id)
    {
        $tipoTrabajo = TipoTrabajo::onlyTrashed()->findOrFail($id);
        $tipoTrabajo->restore();

        return redirect()->route('admin.tipo-trabajos.index')
            ->with('mensaje', 'Tipo de trabajo restaurado exitosamente.')
            ->with('icon', 'success');
    }
}
