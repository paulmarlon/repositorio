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
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="bi bi-journal-check me-1"></i> Bandeja de Trabajos de Grado (Supervisión)</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex flex-wrap justify-content-between align-items-center py-2">
                <span class="font-weight-bold mb-1 mb-md-0">
                    <i class="bi bi-table me-1"></i> Trabajos Postulados - Gestión Activa:
                    <span class="badge bg-info text-dark">
                        {{ $gestionActiva ? $gestionActiva->anio . ' - ' . $gestionActiva->periodo : 'Ninguna' }}
                    </span>
                </span>

                <!-- Botones de Exportación, Colvis y Buscador General -->
                <div class="d-flex flex-wrap gap-2 align-items-center mt-2 mt-md-0">
                    <div class="btn-group btn-group-sm shadow-sm" role="group">
                        <button type="button" class="btn btn-secondary text-white" onclick="copiarPortapapeles()"
                            title="Copiar datos">
                            <i class="bi bi-clipboard"></i> Copiar
                        </button>
                        <button type="button" class="btn btn-success text-white" onclick="exportarExcel()"
                            title="Exportar a Excel">
                            <i class="bi bi-file-earmark-excel"></i> Excel
                        </button>
                        <button type="button" class="btn btn-danger text-white" onclick="exportarPDF()"
                            title="Exportar a PDF">
                            <i class="bi bi-file-earmark-pdf"></i> PDF
                        </button>
                        <button type="button" class="btn btn-info text-white" onclick="window.print()"
                            title="Imprimir listado">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>

                    <!-- Menú desplegable para Columnas (Colvis) -->
                    <div class="dropdown">
                        <button class="btn btn-dark btn-sm dropdown-toggle shadow-sm border-secondary" type="button"
                            id="colvisDropdown" data-bs-toggle="dropdown" aria-expanded="false"
                            title="Mostrar/Ocultar columnas">
                            <i class="bi bi-columns"></i> Columnas
                        </button>
                        <ul class="dropdown-menu dropdown-menu-dark p-2 shadow" aria-labelledby="colvisDropdown"
                            style="font-size: 0.85rem;">
                            <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox"
                                        class="col-toggle me-2" data-column="0" checked> Estudiante</label></li>
                            <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox"
                                        class="col-toggle me-2" data-column="1" checked> Título</label></li>
                            <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox"
                                        class="col-toggle me-2" data-column="2" checked> Tipo / Categoría</label></li>
                            <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox"
                                        class="col-toggle me-2" data-column="3" checked> Archivo</label></li>
                            <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox"
                                        class="col-toggle me-2" data-column="4" checked> Estado / Puntaje</label></li>
                        </ul>
                    </div>

                    <!-- Buscador General -->
                    <div style="width: 200px;">
                        <input type="text" id="buscador-general" class="form-control form-control-sm"
                            placeholder="Buscar trabajo...">
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="tabla-evaluacion"
                        class="table table-bordered table-striped table-hover mb-0 align-middle text-nowrap">
                        <thead class="table-dark">
                            <tr>
                                <th>Cedula</th>
                                <th>Estudiante</th>
                                <th>Título</th>
                                <th>Tipo / Categoría</th>
                                <th>Archivo</th>
                                <th>Estado / Puntaje</th>
                                <th class="text-center" style="width: 15%">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trabajos as $trabajo)
                                <tr>
                                    <td><strong>{{ $trabajo->autor->ci ?? 'N/D' }}</strong></td>
                                    <td><strong>{{ $trabajo->autor->paterno ?? '' }}
                                            {{ $trabajo->autor->materno ?? '' }} {{ $trabajo->autor->nombres ?? 'N/D' }}
                                        </strong></td>
                                    <td class="text-wrap" style="max-width: 350px;">{{ $trabajo->titulo }}</td>
                                    <td>
                                        {{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }} <br>
                                        <small class="text-muted">{{ $trabajo->categoria->nombre ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if ($trabajo->archivo_url)
                                            <a href="{{ asset('storage/' . $trabajo->archivo_url) }}" target="_blank"
                                                class="btn btn-sm btn-outline-danger shadow-sm">
                                                <i class="bi bi-file-earmark-pdf-fill"></i> Ver PDF
                                            </a>
                                        @else
                                            <span class="text-muted small">Sin archivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($trabajo->estado == 'pendiente')
                                            <span class="badge bg-warning text-dark"><i
                                                    class="bi bi-clock-history me-1"></i> Pendiente</span>
                                        @elseif($trabajo->estado == 'aceptado')
                                            <span class="badge bg-success text-white"><i
                                                    class="bi bi-check-circle me-1"></i> Aceptado
                                                ({{ $trabajo->puntaje ?? 0 }} pts)
                                            </span>
                                        @else
                                            <span class="badge bg-danger text-white"><i class="bi bi-x-circle me-1"></i>
                                                Rechazado</span>
                                            @if ($trabajo->motivo_rechazo)
                                                <br><small class="text-muted"
                                                    title="{{ $trabajo->motivo_rechazo }}">{{ $trabajo->motivo_rechazo }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <!-- Botón para abrir modal de evaluación -->
                                        <button type="button" class="btn btn-sm btn-primary shadow-sm"
                                            data-bs-toggle="modal" data-bs-target="#modalEvaluar{{ $trabajo->id }}">
                                            <i class="bi bi-check2-square"></i> Evaluar
                                        </button>

                                        <!-- Modal de Evaluación -->
                                        <div class="modal fade" id="modalEvaluar{{ $trabajo->id }}" tabindex="-1"
                                            aria-hidden="true">
                                            <div class="modal-dialog">
                                                <form action="{{ route('supervisor.trabajos.evaluar', $trabajo->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary text-white py-2">
                                                            <h5 class="modal-title fs-6"><i
                                                                    class="bi bi-pencil-square me-1"></i> Evaluar
                                                                Trabajo
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-start">
                                                            <p class="mb-3"><strong>Título:</strong>
                                                                {{ $trabajo->titulo }}</p>
                                                            <div class="mb-3">
                                                                <label class="form-label font-weight-bold">Estado de
                                                                    Evaluación</label>
                                                                <select name="estado" class="form-select form-select-sm"
                                                                    id="estadoSelect{{ $trabajo->id }}" required
                                                                    onchange="toggleEvaluacionCampos({{ $trabajo->id }})">
                                                                    <option value="aceptado"
                                                                        {{ $trabajo->estado == 'aceptado' ? 'selected' : '' }}>
                                                                        Aceptar</option>
                                                                    <option value="rechazado"
                                                                        {{ $trabajo->estado == 'rechazado' ? 'selected' : '' }}>
                                                                        Rechazar</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3" id="divPuntaje{{ $trabajo->id }}">
                                                                <label class="form-label font-weight-bold">Puntaje (0 -
                                                                    100)</label>
                                                                <input type="number" name="puntaje"
                                                                    class="form-control form-control-sm" min="0"
                                                                    max="100" value="{{ $trabajo->puntaje }}">
                                                            </div>

                                                            <div class="mb-3 d-none" id="divMotivo{{ $trabajo->id }}">
                                                                <label class="form-label font-weight-bold">Motivo de
                                                                    Rechazo</label>
                                                                <textarea name="motivo_rechazo" class="form-control form-control-sm" rows="3">{{ $trabajo->motivo_rechazo }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer py-1">
                                                            <button type="button" class="btn btn-secondary btn-sm"
                                                                data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success btn-sm">Guardar
                                                                Evaluación</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay trabajos en la bandeja
                                        de evaluación para esta gestión.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Librerías de exportación directas (PDFMake) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscadorGeneral = document.getElementById('buscador-general');
            const filas = document.querySelectorAll('#tabla-evaluacion tbody tr');

            // Buscador en tiempo real
            if (buscadorGeneral) {
                buscadorGeneral.addEventListener('keyup', function() {
                    const textoBusqueda = buscadorGeneral.value.toLowerCase();

                    filas.forEach(fila => {
                        const contenidoTexto = fila.textContent.toLowerCase();
                        if (contenidoTexto.includes(textoBusqueda)) {
                            fila.style.display = '';
                        } else {
                            fila.style.display = 'none';
                        }
                    });
                });
            }

            // Control de Visibilidad de Columnas (Colvis nativo)
            document.querySelectorAll('.col-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const colIndex = parseInt(this.getAttribute('data-column'));
                    const isChecked = this.checked;

                    const table = document.getElementById('tabla-evaluacion');
                    for (let row of table.rows) {
                        if (row.cells[colIndex]) {
                            row.cells[colIndex].style.display = isChecked ? '' : 'none';
                        }
                    }
                });
            });
        });

        // Toggle alternar campos de evaluación en modal
        function toggleEvaluacionCampos(id) {
            const estado = document.getElementById('estadoSelect' + id).value;
            const divPuntaje = document.getElementById('divPuntaje' + id);
            const divMotivo = document.getElementById('divMotivo' + id);

            if (estado === 'aceptado') {
                divPuntaje.classList.remove('d-none');
                divMotivo.classList.add('d-none');
            } else {
                divPuntaje.classList.add('d-none');
                divMotivo.classList.remove('d-none');
            }
        }

        // 1. Copiar al Portapapeles
        function copiarPortapapeles() {
            let texto = "";
            let rows = document.querySelectorAll("#tabla-evaluacion tr");
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    let cols = row.querySelectorAll("th, td");
                    let rowData = [];
                    for (let i = 0; i < cols.length - 1; i++) {
                        if (cols[i].style.display !== 'none') {
                            rowData.push(cols[i].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim());
                        }
                    }
                    if (rowData.length > 0) {
                        texto += rowData.join("\t") + "\n";
                    }
                }
            });
            navigator.clipboard.writeText(texto).then(() => {
                alert("¡Datos copiados al portapapeles exitosamente!");
            });
        }

        // 2. Exportar a Excel (CSV)
        function exportarExcel() {
            let csv = [];
            let rows = document.querySelectorAll("#tabla-evaluacion tr");
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    let cols = row.querySelectorAll("th, td");
                    let data = [];
                    for (let i = 0; i < cols.length - 1; i++) {
                        if (cols[i].style.display !== 'none') {
                            let text = cols[i].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim();
                            data.push('"' + text.replace(/"/g, '""') + '"');
                        }
                    }
                    if (data.length > 0) {
                        csv.push(data.join(","));
                    }
                }
            });

            let csvFile = new Blob(["\uFEFF" + csv.join("\n")], {
                type: "text/csv;charset=utf-8;"
            });
            let downloadLink = document.createElement("a");
            downloadLink.download = "Bandeja_Evaluacion_Trabajos.csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        // 3. Exportar a PDF utilizando PDFMake
        function exportarPDF() {
            let bodyData = [];
            let rows = document.querySelectorAll("#tabla-evaluacion tr");

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    let cols = row.querySelectorAll("th, td");
                    let rowData = [];
                    for (let i = 0; i < cols.length - 1; i++) {
                        if (cols[i].style.display !== 'none') {
                            rowData.push({
                                text: cols[i].innerText.trim(),
                                fontSize: 8
                            });
                        }
                    }
                    if (rowData.length > 0) {
                        bodyData.push(rowData);
                    }
                }
            });

            let docDefinition = {
                pageOrientation: 'landscape',
                content: [{
                        text: 'Bandeja de Trabajos de Grado (Supervisión)',
                        fontSize: 16,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    },
                    {
                        table: {
                            headerRows: 1,
                            widths: ['20%', '30%', '20%', '15%', '15%'],
                            body: bodyData
                        }
                    }
                ],
                defaultStyle: {
                    font: 'Roboto'
                }
            };

            pdfMake.createPdf(docDefinition).download('Bandeja_Evaluacion_Trabajos.pdf');
        }
    </script>
@stop
