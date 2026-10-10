@extends('adminlte::page')
@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <h1><b>Editar gestión académica</b></h1>
    <hr>
@stop

@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Modifique los datos del formulario</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.gestiones.update', $gestion->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Año -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="anio">Año</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                                        </div>
                                        <input type="number" class="form-control @error('anio') is-invalid @enderror"
                                            id="anio" name="anio" placeholder="Ej: 2026" min="2020"
                                            max="2050" value="{{ old('anio', $gestion->anio) }}" required>
                                    </div>
                                    @error('anio')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Periodo -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="periodo">Periodo</label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="bi bi-bookmark"></i></span>
                                        </div>
                                        <input type="text" class="form-control @error('periodo') is-invalid @enderror"
                                            id="periodo" name="periodo" placeholder="Ej: Gestión I, Gestión II"
                                            value="{{ old('periodo', $gestion->periodo) }}" required>
                                    </div>
                                    @error('periodo')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Activo -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="activo" name="activo"
                                            value="1" {{ old('activo', $gestion->activo) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="activo">Marcar como gestión
                                            activa</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <a href="{{ route('admin.gestiones.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-save"></i> Actualizar
                                    </button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
@stop

@section('js')
@stop
