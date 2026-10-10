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
                <h1><i class="bi bi-people-fill"></i> Gestión de Usuarios</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <table id="tabla-usuarios" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th>Nombre Completo</th>
                        <th>C.I.</th>
                        <th>Correo Electrónico</th>
                        <th class="text-center" style="width: 15%">Estado</th>
                        <th class="text-center" style="width: 20%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($usuarios as $index => $usuario)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $usuario->nombres }} {{ $usuario->paterno }} {{ $usuario->materno }}</td>
                            <td>{{ $usuario->ci }}</td>
                            <td>{{ $usuario->email }}</td>
                            <td class="text-center">
                                @if ($usuario->activo)
                                    <span class="badge badge-success"><i class="bi bi-check-circle"></i> Habilitado</span>
                                @else
                                    <span class="badge badge-warning text-dark"><i class="bi bi-clock-history"></i>
                                        Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.usuarios.toggle-active', $usuario->id) }}" method="POST">
                                    @csrf
                                    @if ($usuario->activo)
                                        <button type="submit" class="btn btn-sm btn-danger" title="Inhabilitar acceso">
                                            <i class="bi bi-person-dash-fill"></i> Inhabilitar
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-success"
                                            title="Habilitar acceso por única vez">
                                            <i class="bi bi-person-check-fill"></i> Habilitar
                                        </button>
                                    @endif
                                </form>
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
            $('#tabla-usuarios').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });
        });
    </script>

    @include('admin.alertas')
@stop
