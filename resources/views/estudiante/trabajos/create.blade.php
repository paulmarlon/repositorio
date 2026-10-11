@extends('adminlte::page')

@section('title', 'Postular Trabajo de Grado')

@section('content_header')
    <h1>Postular Trabajo de Grado</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('estudiante.trabajos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="gestion_id" value="{{ $gestionActiva->id }}">

                <div class="mb-3">
                    <label for="titulo" class="form-label">Título del Trabajo</label>
                    <input type="text" class="form-control @error('titulo') is-invalid @enderror" id="titulo"
                        name="titulo" value="{{ old('titulo') }}" required>
                    @error('titulo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tipo_id" class="form-label">Tipo de Trabajo</label>
                        <select class="form-select @error('tipo_id') is-invalid @enderror" id="tipo_id" name="tipo_id"
                            required>
                            <option value="">Seleccione un tipo...</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_id') == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('tipo_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="categoria_id" class="form-label">Categoría</label>
                        <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id"
                            name="categoria_id" required>
                            <option value="">Seleccione una categoría...</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}"
                                    {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="resumen" class="form-label">Resumen</label>
                    <textarea class="form-control @error('resumen') is-invalid @enderror" id="resumen" name="resumen" rows="4"
                        required>{{ old('resumen') }}</textarea>
                    @error('resumen')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="archivo_pdf" class="form-label">Documento en PDF (Máx. 10MB)</label>
                    <input type="file" class="form-control @error('archivo_pdf') is-invalid @enderror" id="archivo_pdf"
                        name="archivo_pdf" accept="application/pdf" required>
                    @error('archivo_pdf')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('estudiante.trabajos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success">Guardar y Postular</button>
                </div>
            </form>
        </div>
    </div>
@stop
