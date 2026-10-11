<?php

namespace App\Http\Controllers\Estudiante;

use App\Http\Controllers\Controller;
use App\Models\TrabajoGrado;
use App\Models\TipoTrabajo;
use App\Models\Categoria;
use App\Models\GestionAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrabajoEstudianteController extends Controller
{
    public function index()
    {
        $trabajos = TrabajoGrado::where('autor_id', Auth::id())
            ->with(['tipoTrabajo', 'categoria', 'gestion', 'supervisor'])
            ->get();

        return view('estudiante.trabajos.index', compact('trabajos'));
    }

    public function create()
    {
        $tipos = TipoTrabajo::all();
        $categorias = Categoria::all();
        $gestionActiva = GestionAcademica::where('activo', true)->first();

        if (!$gestionActiva) {
            return redirect()->route('estudiante.trabajos.index')
                ->with('mensaje', 'No hay una gestión académica activa para realizar postulaciones.')
                ->with('icon', 'error');
        }

        return view('estudiante.trabajos.create', compact('tipos', 'categorias', 'gestionActiva'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string',
            'tipo_id' => 'required|exists:tipos_trabajo,id',
            'categoria_id' => 'required|exists:categorias,id',
            'gestion_id' => 'required|exists:gestiones_academicas,id',
            'archivo_pdf' => 'required|mimes:pdf|max:10240', // Máximo 10MB
        ]);

        $path = $request->file('archivo_pdf')->store('trabajos', 'public');

        TrabajoGrado::create([
            'autor_id' => Auth::id(),
            'tipo_id' => $request->tipo_id,
            'categoria_id' => $request->categoria_id,
            'gestion_id' => $request->gestion_id,
            'titulo' => $request->titulo,
            'resumen' => $request->resumen,
            'archivo_url' => $path,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('estudiante.trabajos.index')
            ->with('mensaje', 'Trabajo de grado postulado correctamente.')
            ->with('icon', 'success');
    }
}
