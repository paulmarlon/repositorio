<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\DeviceSession;
use App\Models\User;

class DeviceSessionController extends Controller
{
    public function index()
    {
        // 1. Sesiones Activas en Tiempo Real (para monitoreo y expulsión)
        $activeSessions = DB::table('sessions')
            ->whereNotNull('user_id')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->select(
                'sessions.id',
                'sessions.ip_address',
                'sessions.user_agent',
                'sessions.last_activity',
                'users.nombres',
                'users.paterno',
                'users.materno',
                'users.email'
            )
            ->orderBy('sessions.last_activity', 'desc')
            ->get();

        // 2. Historial de Inicios de Sesión (Registro permanente)
        $historicalSessions = DeviceSession::with('user')
            ->latest('created_at')
            ->paginate(15);

        return view('admin.sesiones.index', compact('activeSessions', 'historicalSessions'));
    }

    public function destroy(string $id)
    {
        // Permite cerrar la sesión del usuario de forma remota eliminando su registro de la tabla
        DB::table('sessions')->where('id', $id)->delete();

        return redirect()->route('admin.sesiones.index')
            ->with('success', 'Sesión revocada y eliminada correctamente.');
    }
}
