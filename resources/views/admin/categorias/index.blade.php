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
                <h1><i class="bi bi-tags-fill"></i> Lista de Categorías</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('admin.categorias.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Nueva Categoría
                </a>
                <a href="{{ route('admin.categorias.papelera') }}" class="btn btn-secondary">
                    <i class="bi bi-trash"></i> Papelera
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="tabla-categorias" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 8%">#</th>
                        <th>Nombre</th>
                        <th style="width: 20%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $index => $categoria)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $categoria->nombre }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.categorias.edit', $categoria->id) }}" class="btn btn-default"
                                        title="Editar">
                                        <i class="bi bi-pencil-square text-success"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.categorias.destroy', $categoria->id) }}" method="POST"
                                        class="d-inline form-eliminar-{{ $categoria->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-default" title="Enviar a papelera"
                                            onclick="confirmarEliminacion({{ $categoria->id }})">
                                            <i class="bi bi-trash-fill text-danger"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $(document).ready(function() {
            $('#tabla-categorias').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });
        });

        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta categoría pasará a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar a papelera',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector('.form-eliminar-' + id).submit();
                }
            });
        }
    </script>

    @include('admin.alertas')
@stop
