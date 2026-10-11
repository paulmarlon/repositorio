@extends('adminlte::page')

@section('title', 'Mis Trabajos de Grado')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Mis Trabajos de Grado</h1>
        <a href="{{ route('estudiante.trabajos.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i> Postular Nuevo Trabajo
        </a>
    </div>
@stop

@section('content')
    @if (session('mensaje'))
        <div class="alert alert-{{ session('icon') == 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
            role="alert">
            {{ session('mensaje') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Categoría</th>
                        <th>Gestión</th>
                        <th>Supervisor</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trabajos as $trabajo)
                        <tr>
                            <td>{{ $trabajo->titulo }}</td>
                            <td>{{ $trabajo->tipoTrabajo->nombre ?? 'N/A' }}</td>
                            <td>{{ $trabajo->categoria->nombre ?? 'N/A' }}</td>
                            <td>{{ $trabajo->gestion->nombre ?? 'N/A' }}</td>
                            <td>{{ $trabajo->supervisor->name ?? 'Sin asignar' }}</td>
                            <td>
                                @if ($trabajo->estado == 'pendiente')
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @elseif($trabajo->estado == 'aceptado')
                                    <span class="badge bg-success">Aceptado</span>
                                @else
                                    <span class="badge bg-danger">Rechazado</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ asset('storage/' . $trabajo->archivo_url) }}" target="_blank"
                                    class="btn btn-sm btn-info" title="Ver PDF">
                                    <i class="bi bi-file-earmark-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No has registrado ningún trabajo de grado
                                todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
