@extends('adminlte::page')

@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <h1><i class="bi bi-journal-richtext"></i> Repositorio Institucional de Trabajos de Grado</h1>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-body">
            <table id="repositorioTable" class="table table-bordered table-hover text-nowrap w-100">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Autor</th>
                        <th>Gestión</th>
                        <th>Tipo / Categoría</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($trabajos as $trabajo)
                        <tr>
                            <td class="text-wrap" style="max-width: 350px;"><strong>{{ $trabajo->titulo }}</strong></td>
                            <td>{{ $trabajo->autor->name ?? 'N/D' }}</td>
                            <td><span class="badge bg-secondary">{{ $trabajo->gestion->nombre ?? 'N/D' }}</span></td>
                            <td>
                                {{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }} <br>
                                <small class="text-muted">{{ $trabajo->categoria->nombre ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('repositorio.show', $trabajo->id) }}" class="btn btn-sm btn-info"
                                    title="Ver resumen y visor">
                                    <i class="bi bi-eye-fill"></i> Ver Resumen
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('css')
    <!-- DataTables CSS Bootstrap 5 / AdminLTE Integration -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@stop

@section('js')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#repositorioTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
                },
                "pageLength": 10,
                "ordering": true,
                "responsive": true
            });
        });
    </script>
@stop
