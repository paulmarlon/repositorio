<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\TrabajoGrado;
use App\Models\User;

class RepositorioController extends Controller
{
    /**
     * Listar todos los trabajos aprobados de cualquier gestión para el repositorio.
     */
    public function index(Request $request)
    {
        // Obtenemos todos los aceptados con ->get() para que DataTables funcione correctamente
        $trabajos = TrabajoGrado::with(['autor', 'tipoTrabajo', 'categoria', 'gestion'])
            ->where('estado', 'aceptado')
            ->latest()
            ->get();

        return view('repositorio.index', compact('trabajos'));
    }

    /**
     * Ver detalles y resumen de un trabajo específico.
     */
    public function show(TrabajoGrado $trabajo)
    {
        /** @var User $user */
        $user = Auth::user();

        $esAutor = $trabajo->autor_id === $user->id;
        $esAdmin = $user->hasRole('Administrador');
        $esSupervisor = $user->hasRole('Supervisor');

        if ($trabajo->estado !== 'aceptado' && !$esAutor && !$esAdmin && !$esSupervisor) {
            abort(403, 'Este trabajo no está disponible públicamente.');
        }

        $trabajo->load(['autor', 'tipoTrabajo', 'categoria', 'gestion', 'supervisor']);

        // URL pública que apuntará este trabajo para validación mediante QR
        $urlValidacion = route('repositorio.show', $trabajo->id);

        // Generador de código QR en formato SVG vía API rápida y segura
        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($urlValidacion);

        return view('repositorio.show', compact('trabajo', 'esAutor', 'esAdmin', 'esSupervisor', 'qrCodeUrl'));
    }

    /**
     * Visor protegido tipo Scribd (Renderiza el PDF sin exponer la ruta física directa).
     */
    /**
     * Visor protegido tipo Scribd (Renderiza el PDF sin exponer la ruta física directa).
     */
    public function visor(TrabajoGrado $trabajo)
    {
        /** @var User $user */
        $user = Auth::user();

        $esAutor = $trabajo->autor_id === $user->id;
        $esAdmin = $user->hasRole('Administrador');
        $esSupervisor = $user->hasRole('Supervisor');

        if ($trabajo->estado !== 'aceptado' && !$esAutor && !$esAdmin && !$esSupervisor) {
            abort(403, 'No autorizado para visualizar este documento.');
        }

        // Determinar en qué disco se encuentra almacenado
        $disk = Storage::disk('public')->exists($trabajo->archivo_url) ? 'public' : 'local';

        if (!$trabajo->archivo_url || !Storage::disk($disk)->exists($trabajo->archivo_url)) {
            abort(404, 'El archivo PDF no se encuentra disponible.');
        }

        // Obtenemos la ruta física absoluta del archivo
        $path = Storage::disk($disk)->path($trabajo->archivo_url);

        // Servimos el archivo con los headers adecuados para visualización en el navegador
        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        ]);
    }
}
