@extends('adminlte::page')

@section('title', $trabajo->titulo)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h1 class="text-dark font-weight-bold">
            <i class="bi bi-file-earmark-text-fill text-primary"></i> Repositorio Institucional
        </h1>
        <a href="{{ route('repositorio.index') }}" class="btn btn-outline-secondary btn-sm shadow-sm">
            <i class="bi bi-arrow-left"></i> Volver al Catálogo
        </a>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Columna Izquierda: Metadatos, Resumen, QR y Descarga -->
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title font-weight-bold m-0"><i class="bi bi-info-circle"></i> Ficha y Metadatos</h3>
                </div>
                <div class="card-body">
                    <!-- Título destacado -->
                    <h5 class="font-weight-bold text-dark mb-3">{{ $trabajo->titulo }}</h5>

                    <ul class="list-unstyled mb-3">
                        <li class="mb-2">
                            <span class="text-muted small d-block">Autor</span>
                            <strong class="text-dark">{{ $trabajo->autor->name ?? 'N/D' }}</strong>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Gestión Académica</span>
                            <span class="badge bg-secondary px-2 py-1">{{ $trabajo->gestion->nombre ?? 'N/D' }}</span>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Tipo de Trabajo / Categoría</span>
                            <strong class="text-dark">{{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }}</strong> <br>
                            <small class="text-muted">{{ $trabajo->categoria->nombre ?? '' }}</small>
                        </li>
                        <li class="mb-2">
                            <span class="text-muted small d-block">Supervisor Evaluador</span>
                            <strong class="text-dark">{{ $trabajo->supervisor->name ?? 'Pendiente / No asignado' }}</strong>
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
                        <div class="p-2 bg-light rounded text-muted text-justify"
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
                                class="btn btn-outline-dark btn-xs">
                                <i class="bi bi-qr-code"></i> Descargar Imagen QR
                            </a>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- BOTÓN DE DESCARGA CONDICIONADO -->
                    @if ($esAutor || $esAdmin || $esSupervisor)
                        <a href="{{ route('repositorio.visor', $trabajo->id) }}" download
                            class="btn btn-success w-100 shadow-sm font-weight-bold">
                            <i class="bi bi-download"></i> Descargar Documento Original
                        </a>
                    @else
                        <div class="alert alert-light border text-center text-secondary small p-2 mb-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-lock-fill text-warning"></i> Documento protegido. Solo lectura pública en
                            plataforma.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Columna Derecha: Visor de PDF Estilo Scribd con Proporción Óptima -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
                    <span class="font-weight-bold"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Visor de
                        Documento Protegido</span>
                    <span class="badge bg-warning text-dark font-weight-bold px-2 py-1">Modo Lectura Segura</span>
                </div>
                <div class="card-body p-0 bg-dark text-center" style="height: 82vh; overflow: hidden;">
                    @if ($trabajo->archivo_url)
                        <iframe src="{{ route('repositorio.visor', $trabajo->id) }}#toolbar=0&navpanes=0&scrollbar=0"
                            width="100%" height="100%" style="border: none;">
                            Este navegador no soporta la visualización de PDFs.
                        </iframe>
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
