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
            <!-- Botón para ver la papelera que añadimos antes -->
            <a href="{{ route('admin.usuarios.roles.papelera') }}" class="btn btn-outline-danger mb-3">
                <i class="bi bi-trash"></i> Ver Papelera de Roles
            </a>
            <table id="tabla-usuarios" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th>Nombre Completo</th>
                        <th>C.I.</th>
                        <th>Correo Electrónico</th>
                        <th>Roles y Gestión</th> <!-- Nueva columna -->
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
                            <td>
                                @forelse($usuario->rolesPorGestion as $asignacion)
                                    <span class="badge badge-info mb-1">
                                        {{ $asignacion->role->name ?? 'Sin Rol' }}
                                        <small>({{ $asignacion->gestion->anio ?? '' }} -
                                            {{ $asignacion->gestion->periodo ?? '' }})</small>
                                    </span><br>
                                @empty
                                    <span class="text-muted font-italic">Sin roles asignados</span>
                                @endforelse
                            </td>
                            <td class="text-center">
                                @if ($usuario->activo)
                                    <span class="badge badge-success"><i class="bi bi-check-circle"></i> Habilitado</span>
                                @else
                                    <span class="badge badge-warning text-dark"><i class="bi bi-clock-history"></i>
                                        Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                    <form action="{{ route('admin.usuarios.toggle', $usuario->id) }}" method="POST"
                                        style="margin: 0;">
                                        @csrf
                                        @method('PATCH')
                                        @if ($usuario->activo)
                                            <button type="submit" class="btn btn-sm btn-danger" title="Inhabilitar acceso">
                                                <i class="bi bi-person-dash-fill"></i> Inhabilitar
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-success" title="Habilitar acceso">
                                                <i class="bi bi-person-check-fill"></i> Habilitar
                                            </button>
                                        @endif
                                    </form>
                                    <a href="{{ route('admin.usuarios.asignar.rol', $usuario->id) }}"
                                        class="btn btn-warning btn-sm text-dark" title="Asignar Roles por Gestión">
                                        <i class="bi bi-person-badge-fill"></i> Roles
                                    </a>

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
            $('#tabla-usuarios').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
                }
            });
        });
    </script>

    @include('admin.alertas')
@stop
