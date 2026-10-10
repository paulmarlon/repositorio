<?php

namespace App\Http\Controllers;

use App\Models\GestionAcademica;
use Illuminate\Http\Request;

class GestionAcademicaController extends Controller
{
    public function index()
    {
        $gestiones = GestionAcademica::all();
        return view('admin.gestiones.index', compact('gestiones'));
    }

    public function create()
    {
        return view('admin.gestiones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'anio' => 'required|integer',
            'periodo' => 'required|string|max:255',
            'activo' => 'nullable|boolean',
        ]);

        // Manejar el checkbox de activo de forma segura
        $data = $request->all();
        $data['activo'] = $request->has('activo') ? 1 : 0;

        GestionAcademica::create($data);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestión académica creada exitosamente.')
            ->with('icon', 'success'); // o 'error', 'warning', 'info'
    }

    public function show(GestionAcademica $gestionAcademica)
    {
        return view('admin.gestiones.show', compact('gestionAcademica'));
    }

    public function edit(int $id)
    {
        $gestion = GestionAcademica::findOrFail($id);
        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(Request $request, GestionAcademica $gestion)
    {
        $request->validate([
            'anio' => 'required|integer',
            'periodo' => 'required|string|max:255',
            'activo' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['activo'] = $request->has('activo') ? 1 : 0;

        $gestion->update($data);

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestión académica actualizada exitosamente.')
            ->with('icon', 'success'); // o 'error', 'warning', 'info'
    }

    public function destroy(GestionAcademica $gestion)
    {
        $gestion->delete();

        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestión académica enviada a la papelera exitosamente.')
            ->with('icon', 'success');
    }


    public function trash()
    {
        $gestiones = GestionAcademica::onlyTrashed()->get();
        return view('admin.gestiones.trash', compact('gestiones'));
    }

    public function restore(int $id)
    {
        $gestionAcademica = GestionAcademica::onlyTrashed()->findOrFail($id);
        $gestionAcademica->restore();
        return redirect()->route('admin.gestiones.index')
            ->with('mensaje', 'Gestión académica restaurada exitosamente..')
            ->with('icon', 'success'); // o 'error', 'warning', 'info'
    }
}
