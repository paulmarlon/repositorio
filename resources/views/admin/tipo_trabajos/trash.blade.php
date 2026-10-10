@extends('adminlte::page')

@section('title', 'Papelera de Tipos de Trabajo')

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="bi bi-trash-fill text-danger"></i> Papelera de Tipos de Trabajo</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.tipo-trabajos.index') }}" class="btn btn-default">
                    <i class="bi bi-check2-circle"></i> Ver activos
                </a>
                <a href="{{ route('admin.tipo-trabajos.papelera') }}" class="btn btn-warning">
                    <i class="bi bi-trash"></i> Ver papelera
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="tabla-tipos-papelera" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Nombre</th>
                        <th style="width: 150px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tipos as $index => $tipo)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $tipo->nombre }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.tipo-trabajos.restaurar', $tipo->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info btn-block"
                                        title="Restaurar tipo de trabajo">
                                        <i class="bi bi-arrow-counterclockwise mr-1"></i> Restaurar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted font-italic">
                                No hay elementos en la papelera.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#tabla-tipos-papelera').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });
        });
    </script>
    @include('admin.alertas')
@stop
