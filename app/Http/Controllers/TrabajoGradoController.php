<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\TrabajoGrado;
use App\Models\GestionAcademica;
use App\Models\TipoTrabajo;
use App\Models\User;
use App\Models\Categoria;

class TrabajoGradoController extends Controller
{
    /**
     * Vista del Estudiante: Listar sus postulaciones en la gestión actual.
     */
    public function indexEstudiante()
    {
        $user = Auth::user();
        $gestionActiva = GestionAcademica::where('activo', true)->first();

        $trabajos = collect();
        if ($gestionActiva) {
            $trabajos = TrabajoGrado::where('autor_id', $user->id)
                ->where('gestion_id', $gestionActiva->id)
                ->with(['tipoTrabajo', 'categoria', 'gestion'])
                ->get();
        }

        return view('estudiante.trabajos.index', compact('trabajos', 'gestionActiva'));
    }

    /**
     * Vista del Estudiante: Formulario de postulación.
     */
    public function create()
    {
        $gestionActiva = GestionAcademica::where('activo', true)->first();

        if (!$gestionActiva) {
            return redirect()->route('estudiante.trabajos.index')
                ->with('error', 'No existe una gestión académica activa para realizar postulaciones.');
        }

        $tiposTrabajo = TipoTrabajo::all();
        $categorias = Categoria::all();

        return view('estudiante.trabajos.create', compact('gestionActiva', 'tiposTrabajo', 'categorias'));
    }

    /**
     * Almacenar la postulación del Estudiante (Subida de PDF y datos).
     */
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'resumen' => 'required|string',
            'tipo_id' => 'required|exists:tipo_trabajos,id',
            'categoria_id' => 'required|exists:categorias,id',
            'archivo' => 'required|mimes:pdf|max:10240', // Máximo 10MB
        ]);

        $gestionActiva = GestionAcademica::where('activo', true)->first();
        if (!$gestionActiva) {
            return back()->withErrors(['error' => 'No hay una gestión académica activa.']);
        }

        /** @var User $user */
        $user = Auth::user();
        $ciAutor = $user && property_exists($user, 'ci') ? $user->ci : ($user->ci ?? 'sincarnet');
        // Preparamos el archivo y su nombre personalizado antes de guardarlo
        $extension = $request->file('archivo')->getClientOriginalExtension();
        $tituloLimpio = Str::slug($request->titulo);

        // Estructura: trabajo_{ci}_{titulo}_{fecha}.pdf (ideal para identificarlo de inmediato)
        $nombrePersonalizado = "trabajo_{$ciAutor}_{$tituloLimpio}_" . date('Ymd_His') . ".{$extension}";

        // Guardamos usando storeAs en lugar de store
        $path = $request->file('archivo')->storeAs('trabajos_grado', $nombrePersonalizado, 'public');

        TrabajoGrado::create([
            'autor_id' => $user->id,
            'tipo_id' => $request->tipo_id,
            'categoria_id' => $request->categoria_id,
            'gestion_id' => $gestionActiva->id,
            'titulo' => $request->titulo,
            'resumen' => $request->resumen,
            'archivo_url' => $path,
            'estado' => 'pendiente',
        ]);

        return redirect()->route('estudiante.trabajos.index')
            ->with('success', 'Trabajo de grado postulado exitosamente y pendiente de revisión.');
    }

    /**
     * Bandeja del Supervisor: Listar trabajos pendientes o evaluados en la gestión activa.
     */
    public function indexSupervisor()
    {
        $gestionActiva = GestionAcademica::where('activo', true)->first();

        $trabajos = collect();
        if ($gestionActiva) {
            $trabajos = TrabajoGrado::where('gestion_id', $gestionActiva->id)
                ->with(['autor', 'tipoTrabajo', 'categoria', 'supervisor'])
                ->latest()
                ->get();
        }

        return view('supervisor.bandeja.index', compact('trabajos', 'gestionActiva'));
    }

    /**
     * Evaluar Trabajo (Aceptar / Rechazar por el Supervisor).
     */
    public function evaluar(Request $request, TrabajoGrado $trabajo)
    {
        $request->validate([
            'estado' => 'required|in:aceptado,rechazado',
            'puntaje' => 'nullable|numeric|min:0|max:100',
            'motivo_rechazo' => 'nullable|string|max:500',
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Si no es Administrador ni es el supervisor asignado (y ya tiene uno asignado)
        if (!$user->hasRole('Administrador') && $trabajo->supervisor_id && $trabajo->supervisor_id !== $user->id) {
            abort(403, 'No estás autorizado para evaluar este trabajo.');
        }
        // Si el trabajo no tenía supervisor asignado aún, asignamos al actual al evaluarlo
        if (!$trabajo->supervisor_id) {
            $trabajo->supervisor_id = Auth::id();
        }

        $trabajo->estado = $request->estado;
        $trabajo->puntaje = $request->estado === 'aceptado' ? $request->puntaje : null;
        $trabajo->motivo_rechazo = $request->estado === 'rechazado' ? $request->motivo_rechazo : null;
        $trabajo->save();

        return redirect()->route('supervisor.bandeja.index')->with('success', 'Trabajo evaluado correctamente.');
    }

    /**
     * Eliminar postulación por parte del Estudiante (solo si está pendiente).
     */
    public function destroy(TrabajoGrado $trabajo)
    {
        // 1. Verificar que el usuario autenticado sea el autor del trabajo
        if ($trabajo->autor_id !== \Illuminate\Support\Facades\Auth::id()) {
            abort(403);
        }

        // 2. Evitar que se eliminen trabajos que ya fueron evaluados (Aceptados/Rechazados)
        if ($trabajo->estado !== 'pendiente') {
            return back()->withErrors(['error' => 'No puedes eliminar un trabajo que ya ha sido evaluado.']);
        }

        // 3. Eliminar el archivo físico de almacenamiento si existe
        if ($trabajo->archivo_url && Storage::disk('public')->exists($trabajo->archivo_url)) {
            Storage::disk('public')->delete($trabajo->archivo_url);
        }

        // 4. Borrar el registro de la base de datos
        $trabajo->delete();

        return redirect()->route('estudiante.trabajos.index')->with('success', 'Trabajo de grado eliminado correctamente.');
    }
}
