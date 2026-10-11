@extends('adminlte::page')

@section('title', 'Bandeja de Evaluación')

@section('content_header')
    <div class="container-fluid">
        <h1>Bandeja de Trabajos de Grado (Supervisión)</h1>
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
                <h3 class="card-title">Trabajos Postulados - Gestión Activa:
                    {{ $gestionActiva ? $gestionActiva->anio . ' - ' . $gestionActiva->periodo : 'Ninguna' }}</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Título</th>
                            <th>Tipo / Categoría</th>
                            <th>Archivo</th>
                            <th>Estado / Puntaje</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($trabajos as $trabajo)
                            <tr>
                                <td>{{ $trabajo->autor->nombres ?? 'N/D' }}</td>
                                <td>{{ $trabajo->titulo }}</td>
                                <td>
                                    {{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }} <br>
                                    <small class="text-muted">{{ $trabajo->categoria->nombre ?? '' }}</small>
                                </td>
                                <td>
                                    @if ($trabajo->archivo_url)
                                        <a href="{{ asset('storage/' . $trabajo->archivo_url) }}" target="_blank"
                                            class="btn btn-sm btn-info">
                                            <i class="bi bi-file-earmark-pdf-fill"></i> Ver PDF
                                        </a>
                                    @else
                                        <span class="text-muted">Sin archivo</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($trabajo->estado == 'pendiente')
                                        <span class="badge bg-warning text-dark">Pendiente</span>
                                    @elseif($trabajo->estado == 'aceptado')
                                        <span class="badge bg-success">Aceptado ({{ $trabajo->puntaje ?? 0 }} pts)</span>
                                    @else
                                        <span class="badge bg-danger">Rechazado</span>
                                        @if ($trabajo->motivo_rechazo)
                                            <br><small class="text-muted" title="{{ $trabajo->motivo_rechazo }}">Ver
                                                motivo</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <!-- Formulario o Botón para abrir modal de evaluación -->
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalEvaluar{{ $trabajo->id }}">
                                        <i class="bi bi-check2-square"></i> Evaluar
                                    </button>

                                    <!-- Modal de Evaluación -->
                                    <div class="modal fade" id="modalEvaluar{{ $trabajo->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="{{ route('supervisor.trabajos.evaluar', $trabajo->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Evaluar Trabajo: {{ $trabajo->titulo }}
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="form-label">Estado de Evaluación</label>
                                                            <select name="estado" class="form-select"
                                                                id="estadoSelect{{ $trabajo->id }}" required
                                                                onchange="toggleEvaluacionCampos({{ $trabajo->id }})">
                                                                <option value="aceptado"
                                                                    {{ $trabajo->estado == 'aceptado' ? 'selected' : '' }}>
                                                                    Aceptar</option>
                                                                <option value="rechazado"
                                                                    {{ $trabajo->estado == 'rechazado' ? 'selected' : '' }}>
                                                                    Rechazar</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3" id="divPuntaje{{ $trabajo->id }}">
                                                            <label class="form-label">Puntaje (0 - 100)</label>
                                                            <input type="number" name="puntaje" class="form-control"
                                                                min="0" max="100"
                                                                value="{{ $trabajo->puntaje }}">
                                                        </div>

                                                        <div class="mb-3 d-none" id="divMotivo{{ $trabajo->id }}">
                                                            <label class="form-label">Motivo de Rechazo</label>
                                                            <textarea name="motivo_rechazo" class="form-control" rows="3">{{ $trabajo->motivo_rechazo }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancelar</option>
                                                            <button type="submit" class="btn btn-success">Guardar
                                                                Evaluación</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay trabajos en la bandeja de
                                    evaluación para esta gestión.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                @push('js')
                    <script>
                        function toggleEvaluacionCampos(id) {
                            const estado = document.getElementById('estadoSelect' + id).value;
                            const divPuntaje = document.getElementById('divPuntaje' + id);
                            const divMotivo = document.getElementById('divMotivo' + id);

                            if (estado === 'aceptado') {
                                divPuntaje.classList.remove('d-none');
                                divMotivo.classList.add('d-none');
                            } else {
                                divPuntaje.classList.add('d-none');
                                divMotivo.classList.remove('d-none');
                            }
                        }
                    </script>
                @endpush
            </div>
        </div>
    </div>
@stop
