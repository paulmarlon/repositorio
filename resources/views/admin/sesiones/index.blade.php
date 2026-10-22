@extends('adminlte::page')

@section('title', 'Monitoreo de Sesiones')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Monitoreo y Auditoría de Sesiones</h1>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-primary card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs" id="sessionTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="online-tab" data-bs-toggle="tab" href="#online-content"
                            role="tab">
                            <i class="bi bi-broadcast text-success me-1"></i> Sesiones en Línea (En Tiempo Real)
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history-content" role="tab">
                            <i class="bi bi-clock-history text-info me-1"></i> Historial de Inicios de Sesión
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="sessionTabsContent">

                    {{-- PESTAÑA 1: SESIONES EN LÍNEA (PARA EXPULSAR) --}}
                    <div class="tab-pane fade show active" id="online-content" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Email</th>
                                        <th>Dirección IP</th>
                                        <th>Navegador / Dispositivo</th>
                                        <th>Última Actividad</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($activeSessions as $session)
                                        <tr>
                                            <td><strong>{{ $session->nombres }} {{ $session->paterno }}</strong></td>
                                            <td>{{ $session->email }}</td>
                                            <td><span class="badge bg-primary">{{ $session->ip_address }}</span></td>
                                            <td style="max-width: 280px;" class="text-truncate"
                                                title="{{ $session->user_agent }}">
                                                {{ $session->user_agent }}
                                            </td>
                                            <td>{{ \Carbon\Carbon::createFromTimestamp($session->last_activity)->diffForHumans() }}
                                            </td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.sesiones.destroy', $session->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm"
                                                        onclick="return confirm('¿Deseas cerrar/revocar esta sesión?')">
                                                        <i class="bi bi-box-arrow-right me-1"></i> Revocar
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-3">No hay sesiones activas
                                                de usuarios.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- PESTAÑA 2: HISTORIAL PERMANENTE --}}
                    <div class="tab-pane fade" id="history-content" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>IP</th>
                                        <th>Dispositivo</th>
                                        <th>Navegador / Agente</th>
                                        <th>Fecha de Ingreso</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($historicalSessions as $history)
                                        <tr>
                                            <td>{{ $history->user ? $history->user->nombres . ' ' . $history->user->paterno : 'Usuario eliminado' }}
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $history->ip_address }}</span></td>
                                            <td>
                                                <span
                                                    class="badge {{ $history->device_type === 'Móvil / Tablet' ? 'bg-warning text-dark' : 'bg-info' }}">
                                                    {{ $history->device_type }}
                                                </span>
                                            </td>
                                            <td style="max-width: 280px;" class="text-truncate"
                                                title="{{ $history->user_agent }}">
                                                {{ $history->user_agent }}
                                            </td>
                                            <td>{{ $history->created_at ? $history->created_at->format('d/m/Y H:i:s') : 'N/A' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-3">No hay registros
                                                almacenados en el historial aún.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Paginador nativo de Bootstrap 5 --}}
                        <div class="d-flex justify-content-center mt-4">
                            {{ $historicalSessions->links('pagination::bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Mantener abierta la pestaña de historial si se está usando la paginación (?page=...)
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('page')) {
                const historyTab = document.querySelector('#history-tab');
                const historyContent = document.querySelector('#history-content');

                const onlineTab = document.querySelector('#online-tab');
                const onlineContent = document.querySelector('#online-content');

                if (historyTab && historyContent) {
                    onlineTab.classList.remove('active');
                    onlineContent.classList.remove('show', 'active');

                    historyTab.classList.add('active');
                    historyContent.classList.add('show', 'active');
                }
            }

            // Guardar pestaña activa en localStorage
            const triggerTabList = document.querySelectorAll('#sessionTabs a');
            triggerTabList.forEach(function(triggerEl) {
                triggerEl.addEventListener('click', function(event) {
                    event.preventDefault();
                    const tabTrigger = new bootstrap.Tab(this);
                    tabTrigger.show();
                    localStorage.setItem('activeSessionTab', this.getAttribute('href'));
                });
            });

            // Restaurar pestaña activa si no hay paginación de por medio
            const activeTab = localStorage.getItem('activeSessionTab');
            if (activeTab && !urlParams.has('page')) {
                const someTabElement = document.querySelector(`#sessionTabs a[href="${activeTab}"]`);
                if (someTabElement) {
                    const tab = new bootstrap.Tab(someTabElement);
                    tab.show();
                }
            }
        });
    </script>
@endsection
