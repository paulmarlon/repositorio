@extends('adminlte::page')

@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <h1><b>Editar Tipo de Trabajo</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-layer-group mr-2"></i> Actualizar Datos del Tipo de Trabajo</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.tipo-trabajos.update', $tipoTrabajo->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nombre">Nombre del Tipo de Trabajo (*)</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre"
                                name="nombre" value="{{ old('nombre', $tipoTrabajo->nombre) }}"
                                placeholder="Ej: Tesis, Monografía, Proyecto de Grado..." required>
                            @error('nombre')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="text-right mt-4">
                            <a href="{{ route('admin.tipo-trabajos.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Actualizar Tipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
