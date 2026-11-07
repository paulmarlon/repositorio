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

// ... validaciones previas ...

        if ($request->hasFile('archivo_pdf')) {
            // 1. Obtenemos la extensión (debería ser pdf por la validación)
            $extension = $request->file('archivo_pdf')->getClientOriginalExtension();

            // 2. Construimos un nombre personalizado (ej: trabajo_autor_12_20261104_193500_6543abc.pdf)
            $nombrePersonalizado = 'trabajo_autor_' . Auth::id() . '_' . date('Ymd_His') . '_' . uniqid() . '.' . $extension;

            // 3. Guardamos usando storeAs para mantener la compatibilidad agnóstica (Local/S3)
            $path = $request->file('archivo_pdf')->storeAs('trabajos', $nombrePersonalizado, 'public');
        }

        TrabajoGrado::create([
            'autor_id' => Auth::id(),
            'tipo_id' => $request->tipo_id,
            'categoria_id' => $request->categoria_id,
            'gestion_id' => $request->gestion_id,
            'titulo' => $request->titulo,
            'resumen' => $request->resumen,
            'archivo_url' => $path ?? null, // Aseguramos que tome la ruta generada
            'estado' => 'pendiente',
        ]);

        return redirect()->route('estudiante.trabajos.index')
            ->with('mensaje', 'Trabajo de grado postulado correctamente.')
            ->with('icon', 'success');
    }
}
