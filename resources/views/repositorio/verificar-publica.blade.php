@php
    $configuracion = \App\Models\ConfiguracionInstituto::first();
@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Constancia de Verificación - {{ $configuracion->nombre_instituto ?? 'Repositorio Institucional' }}</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body class="bg-light d-flex flex-column justify-content-center align-items-center min-vh-100 py-4">

    <div class="container" style="max-width: 650px;">
        <div class="card shadow border-0 rounded-4">
            <div class="card-header bg-primary text-white text-center py-4 rounded-top-4">
                @if (isset($configuracion) && $configuracion->logo)
                    <img src="{{ asset('storage/' . $configuracion->logo) }}" alt="Logo"
                        class="mb-2 rounded-circle bg-white p-1"
                        style="width: 60px; height: 60px; object-fit: contain;">
                @else
                    <i class="bi bi-patch-check-fill display-4 text-warning mb-2"></i>
                @endif
                <small
                    class="text-white-50">{{ $configuracion->nombre_instituto ?? 'Repositorio Institucional de Trabajos de Grado' }}</small>
            </div>
            <div class="card-body p-4 p-md-5">
                <div class="alert alert-success text-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <strong>Documento Verificado y Legítimo</strong><br>
                    <small>Este registro se encuentra oficialmente aceptado en la base de datos de la
                        institución.</small>
                </div>

                <h4 class="text-dark font-weight-bold mb-3 text-center">{{ $trabajo->titulo }}</h4>

                <hr class="my-4">

                <div class="row mb-3">
                    <div class="col-sm-4 text-muted font-weight-bold">Autor(es):</div>
                    <div class="col-sm-8 text-dark fw-semibold">{{ $trabajo->autor->name ?? 'N/D' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted font-weight-bold">Gestión Académica:</div>
                    <div class="col-sm-8">
                        <span class="badge bg-secondary">
                            @if ($trabajo->gestion)
                                {{ $trabajo->gestion->anio }} - {{ $trabajo->gestion->periodo }}
                            @else
                                N/D
                            @endif
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted font-weight-bold">Tipo de Trabajo:</div>
                    <div class="col-sm-8 text-dark">{{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted font-weight-bold">Categoría:</div>
                    <div class="col-sm-8 text-dark">{{ $trabajo->categoria->nombre ?? 'N/D' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted font-weight-bold">Supervisor Evaluador:</div>
                    <div class="col-sm-8 text-dark">{{ $trabajo->supervisor->name ?? 'N/D' }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4 text-muted font-weight-bold">Calificación:</div>
                    <div class="col-sm-8"><span class="badge bg-success">{{ $trabajo->puntaje ?? 'N/D' }} pts</span>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <span class="text-uppercase text-muted font-weight-bold small d-block mb-1">Resumen /
                        Abstract</span>
                    <p class="text-muted text-justify small" style="white-space: pre-line; line-height: 1.6;">
                        {{ $trabajo->resumen }}
                    </p>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-house-door"></i> Ir al Portal Institucional
                    </a>
                </div>
            </div>
            <div class="card-footer bg-light text-center text-muted small py-3 rounded-bottom-4">
                {{ $configuracion->nombre_instituto ?? 'Sistema de Repositorio Académico' }} &bull; Validación Segura
                por Código QR
            </div>
        </div>
    </div>

</body>

</html>
