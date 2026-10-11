@extends('adminlte::page')

@section('title', 'Postular Trabajo de Grado')

@section('content_header')
    <div class="container-fluid">
        <h1>Postular Nuevo Trabajo de Grado</h1>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Formulario de Registro (Gestión: {{ $gestionActiva->anio }} -
                    {{ $gestionActiva->periodo }})</h3>
            </div>
            <form action="{{ route('estudiante.trabajos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="titulo">Título del Trabajo</label>
                        <input type="text" name="titulo" class="form-control" id="titulo" value="{{ old('titulo') }}"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_id">Tipo de Trabajo</label>
                                <select name="tipo_id" id="tipo_id" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($tiposTrabajo as $tipo)
                                        <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="categoria_id">Categoría</label>
                                <select name="categoria_id" id="categoria_id" class="form-control" required>
                                    <option value="">Seleccione...</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="resumen">Resumen / Descripción</label>
                        <textarea name="resumen" id="resumen" rows="4" class="form-control" required>{{ old('resumen') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label for="archivo">Documento PDF (Máx. 10MB)</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" name="archivo" class="custom-file-input" id="archivo"
                                    accept="application/pdf" required>
                                <label class="custom-file-label" for="archivo">Seleccionar archivo PDF</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enviar Postulación</button>
                    <a href="{{ route('estudiante.trabajos.index') }}" class="btn btn-default">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@stop
