@extends('adminlte::page')

@section('title', 'Papelera de Roles por Gestión')

@section('content_header')
    <h1><i class="bi bi-trash-fill text-danger"></i> Papelera de Roles y Gestiones</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="bi bi-clock-history"></i> Registros Eliminados (Soft Deletes)</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol Asignado</th>
                        <th>Gestión Académica</th>
                        <th>Fecha de Eliminación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rolesEliminados as $item)
                        <tr>
                            <td><b>{{ $item->user->nombres ?? 'Usuario no encontrado' }}
                                    {{ $item->user->paterno ?? '' }}</b></td>
                            <td><span class="badge badge-secondary">{{ $item->role->name ?? 'Sin Rol' }}</span></td>
                            <td>{{ $item->gestion->anio ?? '' }} - {{ $item->gestion->periodo ?? '' }}</td>
                            <td>{{ $item->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <form action="{{ route('admin.usuarios.roles.restaurar', $item->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">La papelera de asignaciones está vacía.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary mb-3">
        <i class="bi bi-arrow-left-circle-fill"></i> Volver al listado de usuarios
    </a>
@stop

@section('js')
    @include('admin.alertas')
@stop
