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

                    <!-- Sección dividida: Selector de archivo a la izquierda y Vista Previa al costado -->
                    <div class="row border-top pt-3 mt-3">
                        <div class="col-md-7">
                            <div class="form-group">
                                <label for="archivo">Documento PDF (Máx. 10MB)</label>
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" name="archivo" class="custom-file-input" id="archivo"
                                            accept="application/pdf" required onchange="previewPdf(event)">
                                        <label class="custom-file-label" for="archivo" id="archivo-label">Seleccionar
                                            archivo PDF</label>
                                    </div>
                                </div>
                                <small class="form-text text-muted mt-2">
                                    Sube el documento final en formato PDF. Asegúrate de que la primera página sea la
                                    portada oficial.
                                </small>
                            </div>
                        </div>

                        <div class="col-md-5 text-center">
                            <!-- Contenedor para la vista previa al costado -->
                            <div id="pdf-preview-container" style="display: none;">
                                <label class="text-muted d-block text-left">Vista previa (Portada):</label>
                                <div class="p-2 bg-light border rounded d-inline-block shadow-sm" style="max-width: 100%;">
                                    <canvas id="pdf-canvas"
                                        style="max-height: 220px; width: auto; display: block; margin: 0 auto;"></canvas>
                                </div>
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

@push('js')
    <!-- Cargamos PDF.js desde CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';

        function previewPdf(event) {
            const file = event.target.files[0];
            if (!file || file.type !== 'application/pdf') return;

            // Actualizar el texto del input con el nombre del archivo seleccionado
            document.getElementById('archivo-label').innerText = file.name;

            const reader = new FileReader();
            reader.onload = function(e) {
                const typedarray = new Uint8Array(e.target.result);

                pdfjsLib.getDocument(typedarray).promise.then(function(pdfDoc_) {
                    pdfDoc_.getPage(1).then(function(page) {
                        const scale = 1.2;
                        const viewport = page.getViewport({
                            scale: scale
                        });

                        const canvas = document.getElementById('pdf-canvas');
                        const context = canvas.getContext('2d');
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        const renderContext = {
                            canvasContext: context,
                            viewport: viewport
                        };

                        page.render(renderContext).promise.then(function() {
                            document.getElementById('pdf-preview-container').style.display =
                                'block';
                        });
                    });
                });
            };
            reader.readAsArrayBuffer(file);
        }
    </script>
@endpush
