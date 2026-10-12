@extends('adminlte::page')

@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Mis Trabajos de Grado</h1>
            </div>
            <div class="col-sm-6 text-right">
                @if ($gestionActiva)
                    <a href="{{ route('estudiante.trabajos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Postular Nuevo Trabajo
                    </a>
                @else
                    <span class="badge badge-warning p-2">No hay gestión académica activa para postular</span>
                @endif
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Gestión Actual:
                    {{ $gestionActiva ? $gestionActiva->anio . ' - ' . $gestionActiva->periodo : 'Ninguna' }}</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Título</th>
                            <th>Tipo</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Archivo</th>
                            <th>Puntaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trabajos as $trabajo)
                            <tr>
                                <td>{{ $trabajo->titulo }}</td>
                                <td>{{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }}</td>
                                <td>{{ $trabajo->categoria->nombre ?? 'N/D' }}</td>
                                <td>
                                    @if ($trabajo->estado == 'pendiente')
                                        <span class="badge badge-warning">Pendiente</span>
                                    @elseif($trabajo->estado == 'aceptado')
                                        <span class="badge badge-success">Aceptado</span>
                                    @else
                                        <span class="badge badge-danger">Rechazado</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($trabajo->archivo_url)
                                        <a href="{{ asset('storage/' . $trabajo->archivo_url) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-file-pdf"></i> Ver PDF
                                        </a>
                                    @else
                                        <span class="text-muted">Sin archivo</span>
                                    @endif
                                </td>
                                <td>{{ $trabajo->puntaje ?? '-' }}</td>
                                <td>
                                    @if ($trabajo->estado == 'pendiente')
                                        <form action="{{ route('estudiante.trabajos.destroy', $trabajo->id) }}"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Estás seguro de eliminar esta postulación?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                title="Eliminar postulación">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">No editable</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No has registrado ningún trabajo de
                                    grado en esta gestión.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop
