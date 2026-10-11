@extends('adminlte::page')


@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="font-weight-bold">
            <i class="bi bi-file-earmark-text-fill text-primary"></i> Repositorio Institucional
        </h1>
        <a href="{{ route('repositorio.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Columna Izquierda: Metadatos, Resumen y QR -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title font-weight-bold m-0"><i class="bi bi-info-circle"></i> Ficha y Metadatos</h3>
                </div>
                <div class="card-body">
                    <!-- Título destacado -->
                    <h5 class="font-weight-bold mb-3">{{ $trabajo->titulo }}</h5>

                    <ul class="list-unstyled mb-3">
                        <li class="mb-2">
                            <span class="text-muted small d-block">Autor</span>
                            <strong class="font-weight-semibold">{{ $trabajo->autor->name }}
                                {{ $trabajo->autor->paterno }} {{ $trabajo->autor->materno }}</strong>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Gestión Académica</span>
                            <span class="badge bg-secondary px-2 py-1">
                                @if ($trabajo->gestion)
                                    {{ $trabajo->gestion->anio }} - {{ $trabajo->gestion->periodo }}
                                @else
                                    N/D
                                @endif
                            </span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Tipo de Trabajo / Categoría</span>
                            <strong class="font-weight-semibold">{{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }}</strong> <br>
                            <small class="text-muted">{{ $trabajo->categoria->nombre ?? '' }}</small>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Transcripción Calificación</span>
                            <strong
                                class="font-weight-semibold">{{ $trabajo->supervisor->name ?? 'Pendiente / No asignado' }}</strong>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Puntaje Obtenido</span>
                            <span class="badge bg-success px-2 py-1">{{ $trabajo->puntaje ?? 'N/D' }} pts</span>
                        </li>
                    </ul>

                    <hr class="my-3">

                    <!-- Resumen Integrado dentro de Metadatos -->
                    <div class="mb-3">
                        <span class="text-uppercase text-muted font-weight-bold small d-block mb-1">
                            <i class="bi bi-card-text"></i> Resumen / Abstract
                        </span>
                        <div class="p-2 border rounded text-justify"
                            style="max-height: 180px; overflow-y: auto; font-size: 0.9rem; line-height: 1.5; white-space: pre-line;">
                            {{ $trabajo->resumen }}
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Código QR de Verificación Oficial -->
                    <div class="text-center mb-3">
                        <span class="text-muted small d-block mb-2 font-weight-bold">Código QR de Validación Oficial</span>
                        <div class="bg-white p-2 d-inline-block border rounded shadow-sm">
                            <img src="{{ $qrCodeUrl }}" alt="QR de Validación" class="img-fluid"
                                style="width: 130px; height: 130px;">
                        </div>
                        <div class="mt-2">
                            <a href="{{ $qrCodeUrl }}" download="QR-Trabajo-{{ $trabajo->id }}.png" target="_blank"
                                class="btn btn-outline-secondary btn-xs">
                                <i class="bi bi-qr-code"></i> Descargar Imagen QR
                            </a>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- AVISO DE PROTECCIÓN DE CONTENIDO -->
                    <div class="alert alert-secondary border text-center small p-2 mb-0 shadow-sm" role="alert">
                        <i class="bi bi-shield-lock-fill text-warning"></i> Documento protegido bajo políticas de propiedad
                        intelectual. Solo visualización en línea.
                    </div>
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Visor Protegido con PDF.js (Sin opción a descarga) -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <span class="font-weight-bold">
                        <i class="bi bi-shield-lock-fill text-warning me-2"></i> Visor Protegido (Modo Lectura Exclusiva)
                    </span>
                    <div class="d-flex align-items-center gap-2">
                        <button id="prev-page" class="btn btn-outline-light btn-xs me-1"><i
                                class="bi bi-chevron-left"></i></button>
                        <span class="text-white small">Página <span id="page-num">1</span> de <span
                                id="page-count">--</span></span>
                        <button id="next-page" class="btn btn-outline-light btn-xs ms-1"><i
                                class="bi bi-chevron-right"></i></button>
                    </div>
                </div>

                <div class="card-body p-0 bg-secondary text-center position-relative"
                    style="height: 82vh; overflow-y: auto;" id="pdf-container" oncontextmenu="return false;">

                    @if ($trabajo->archivo_url)
                        <!-- Canvas donde se renderiza el PDF sin entregarlo al visor del navegador -->
                        <canvas id="pdf-render" class="shadow my-3" style="max-width: 95%; height: auto;"></canvas>
                    @else
                        <div class="p-5 text-white d-flex flex-column justify-content-center align-items-center h-100">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning mb-2"></i>
                            <p class="m-0">El documento no cuenta con archivo digital adjunto en el sistema.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
@section('js')
    <!-- Carga de PDF.js desde CDN oficial -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const url = "{{ route('repositorio.visor', $trabajo->id) }}";

            pdfjsLib.GlobalWorkerOptions.workerSrc =
                'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

            let pdfDoc = null,
                pageNum = 1,
                pageIsRendering = false,
                pageNumIsPending = null;

            const scale = 1.3,
                canvas = document.getElementById('pdf-render'),
                ctx = canvas ? canvas.getContext('2d') : null;

            if (canvas) {
                // Renderizar la página especificada
                const renderPage = num => {
                    pageIsRendering = true;

                    pdfDoc.getPage(num).then(page => {
                        const viewport = page.getViewport({
                            scale
                        });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        const renderCtx = {
                            canvasContext: ctx,
                            viewport
                        };

                        page.render(renderCtx).promise.then(() => {
                            pageIsRendering = false;

                            if (pageNumIsPending !== null) {
                                renderPage(pageNumIsPending);
                                pageNumIsPending = null;
                            }
                        });

                        document.getElementById('page-num').textContent = num;
                    });
                };

                const queueRenderPage = num => {
                    if (pageIsRendering) {
                        pageNumIsPending = num;
                    } else {
                        renderPage(num);
                    }
                };

                // Mostrar página anterior
                document.getElementById('prev-page').addEventListener('click', () => {
                    if (pageNum <= 1) return;
                    pageNum--;
                    queueRenderPage(pageNum);
                });

                // Mostrar página siguiente
                document.getElementById('next-page').addEventListener('click', () => {
                    if (pageNum >= pdfDoc.numPages) return;
                    pageNum++;
                    queueRenderPage(pageNum);
                });

                // Cargar el documento PDF de forma segura
                pdfjsLib.getDocument(url).promise.then(pdfDoc_ => {
                    pdfDoc = pdfDoc_;
                    document.getElementById('page-count').textContent = pdfDoc.numPages;
                    renderPage(pageNum);
                });

                // Bloquear atajos de teclado para guardar/imprimir (Ctrl+S, Ctrl+P)
                document.addEventListener('keydown', function(e) {
                    if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'p')) {
                        e.preventDefault();
                        alert('Acción restringida por protección de derechos de autor.');
                    }
                });
            }
        });
    </script>
@stop
