@extends('adminlte::page')

@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <h1><i class="bi bi-person-badge-fill"></i> Asignar Roles: <b>{{ $usuario->nombres }} {{ $usuario->paterno }}
            {{ $usuario->materno }}</b></h1>
@stop

@section('content')
    <div class="row">
        <!-- Formulario de asignación -->
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-plus-circle"></i> Nueva Asignación</h3>
                </div>
                <form action="{{ route('admin.usuarios.guardar.rol', $usuario->id) }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="role_id">Rol:</label>
                            <select name="role_id" id="role_id" class="form-control" required>
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="gestion_id">Gestión Académica:</label>
                            <select name="gestion_id" id="gestion_id" class="form-control" required>
                                <option value="">Seleccione una gestión</option>
                                @foreach ($gestiones as $gestion)
                                    <option value="{{ $gestion->id }}">{{ $gestion->anio }} - {{ $gestion->periodo }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block"><i class="bi bi-save"></i> Guardar
                            Rol</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado de roles actuales por gestión -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-shield-lock"></i> Roles Activos por Gestión</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Rol</th>
                                <th>Gestión Académica</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rolesAsignados as $asignacion)
                                <tr>
                                    <td><span class="badge badge-info">{{ $asignacion->role->name ?? 'Sin Rol' }}</span>
                                    </td>
                                    <td>{{ $asignacion->gestion->anio ?? '' }} - {{ $asignacion->gestion->periodo ?? '' }}
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('admin.usuarios.eliminar.rol', $asignacion->id) }}"
                                            method="POST" class="form-eliminar" style="display: inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-sm btn-eliminar">
                                                <i class="bi bi-trash-fill"></i> Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Este usuario no tiene roles
                                        asignados en ninguna gestión.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary mb-3">
                <i class="bi bi-arrow-left-circle-fill"></i> Volver al listado de usuarios
            </a>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-eliminar').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    let form = this.closest('form');
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Esta acción quitará el rol asignado.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
    @include('admin.alertas')
@stop
