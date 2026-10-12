@extends('adminlte::page')

@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="bi bi-journal-richtext"></i> Repositorio Institucional de Trabajos de Grado</h1>
    </div>
@stop

@section('content')
    <!-- Filtros de búsqueda avanzada nativos -->
    <div class="card card-outline card-primary shadow-sm mb-3">
        <div class="card-body py-3">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="filtro-gestion" class="form-label font-weight-bold small">Gestión Académica</label>
                    <select id="filtro-gestion" class="form-control form-control-sm">
                        <option value="">Todas las gestiones</option>
                        @foreach ($gestiones as $gestion)
                            <option value="{{ $gestion->anio }} - {{ $gestion->periodo }}">
                                {{ $gestion->anio }} - {{ $gestion->periodo }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro-tipo" class="form-label font-weight-bold small">Tipo de Trabajo</label>
                    <select id="filtro-tipo" class="form-control form-control-sm">
                        <option value="">Todos los tipos</option>
                        @foreach ($tiposTrabajo as $tipo)
                            <option value="{{ $tipo->nombre }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filtro-categoria" class="form-label font-weight-bold small">Categoría</label>
                    <select id="filtro-categoria" class="form-control form-control-sm">
                        <option value="">Todas las categorías</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->nombre }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 text-end">
                    <button type="button" id="btn-limpiar-filtros"
                        class="btn btn-outline-secondary btn-sm w-100 shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Limpiar Filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla Principal con Botones de Exportación Completos (Copiar, Excel, PDF, Imprimir, Columnas) -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex flex-wrap justify-content-between align-items-center py-2">
            <span class="font-weight-bold mb-1 mb-md-0"><i class="bi bi-table me-1"></i> Listado de Trabajos</span>

            <div class="d-flex flex-wrap gap-2 align-items-center">
                <!-- Grupo de Botones Estilo DataTables -->
                <div class="btn-group btn-group-sm shadow-sm" role="group">
                    <button type="button" class="btn btn-secondary text-white" onclick="copiarPortapapeles()"
                        title="Copiar datos">
                        <i class="bi bi-clipboard"></i> Copiar
                    </button>
                    <button type="button" class="btn btn-success text-white" onclick="exportarExcel()"
                        title="Exportar a Excel">
                        <i class="bi bi-file-earmark-excel"></i> Excel
                    </button>
                    <button type="button" class="btn btn-danger text-white" onclick="exportarPDF()" title="Exportar a PDF">
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
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="0" checked> Título</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="1" checked> Autor</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="2" checked> Gestión</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="3" checked> Tipo de Trabajo</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="4" checked> Categoría</label></li>
                    </ul>
                </div>

                <!-- Buscador General -->
                <div style="width: 200px;">
                    <input type="text" id="buscador-general" class="form-control form-control-sm"
                        placeholder="Buscar...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="repositorioTable" class="table table-bordered table-hover text-nowrap mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Cedula</th>
                            <th>Gestión</th>
                            <th>Tipo de Trabajo</th>
                            <th>Categoría</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trabajos as $trabajo)
                            <tr data-gestion="{{ $trabajo->gestion->anio ?? 'N/D' }} - {{ $trabajo->gestion->periodo ?? '' }}"
                                data-tipo="{{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }}"
                                data-categoria="{{ $trabajo->categoria->nombre ?? 'N/D' }}">
                                <td class="text-wrap" style="max-width: 350px;"><strong>{{ $trabajo->titulo }}</strong>
                                </td>
                                <td>{{ $trabajo->autor->paterno ?? '' }} {{ $trabajo->autor->materno ?? '' }}
                                    {{ $trabajo->autor->nombres ?? 'N/D' }}</td>
                                <td>{{ $trabajo->autor->ci ?? 'N/D' }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $trabajo->gestion->anio ?? 'N/D' }} - {{ $trabajo->gestion->periodo ?? '' }}
                                    </span>
                                </td>
                                <td>{{ $trabajo->tipoTrabajo->nombre ?? 'N/D' }}</td>
                                <td>{{ $trabajo->categoria->nombre ?? 'N/D' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('repositorio.show', $trabajo->id) }}"
                                        class="btn btn-sm btn-info shadow-sm" title="Ver resumen y visor">
                                        <i class="bi bi-eye-fill text-white"></i> Ver Resumen
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Librerías de exportación directas (PDFMake y JSZip) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filtroGestion = document.getElementById('filtro-gestion');
            const filtroTipo = document.getElementById('filtro-tipo');
            const filtroCategoria = document.getElementById('filtro-categoria');
            const buscadorGeneral = document.getElementById('buscador-general');
            const btnLimpiar = document.getElementById('btn-limpiar-filtros');
            const filas = document.querySelectorAll('#repositorioTable tbody tr');

            function filtrarTabla() {
                const valGestion = filtroGestion.value.toLowerCase();
                const valTipo = filtroTipo.value.toLowerCase();
                const valCategoria = filtroCategoria.value.toLowerCase();
                const textoBusqueda = buscadorGeneral.value.toLowerCase();

                filas.forEach(fila => {
                    const gestion = fila.getAttribute('data-gestion').toLowerCase();
                    const tipo = fila.getAttribute('data-tipo').toLowerCase();
                    const categoria = fila.getAttribute('data-categoria').toLowerCase();
                    const contenidoTexto = fila.textContent.toLowerCase();

                    const coincideGestion = !valGestion || gestion.includes(valGestion);
                    const coincideTipo = !valTipo || tipo.includes(valTipo);
                    const coincideCategoria = !valCategoria || categoria.includes(valCategoria);
                    const coincideTexto = !textoBusqueda || contenidoTexto.includes(textoBusqueda);

                    if (coincideGestion && coincideTipo && coincideCategoria && coincideTexto) {
                        fila.style.display = '';
                    } else {
                        fila.style.display = 'none';
                    }
                });
            }

            filtroGestion.addEventListener('change', filtrarTabla);
            filtroTipo.addEventListener('change', filtrarTabla);
            filtroCategoria.addEventListener('change', filtrarTabla);
            buscadorGeneral.addEventListener('keyup', filtrarTabla);

            btnLimpiar.addEventListener('click', function() {
                filtroGestion.value = '';
                filtroTipo.value = '';
                filtroCategoria.value = '';
                buscadorGeneral.value = '';
                filtrarTabla();
            });

            // Control de Visibilidad de Columnas (Colvis nativo)
            document.querySelectorAll('.col-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const colIndex = parseInt(this.getAttribute('data-column'));
                    const isChecked = this.checked;

                    const table = document.getElementById('repositorioTable');
                    for (let row of table.rows) {
                        if (row.cells[colIndex]) {
                            row.cells[colIndex].style.display = isChecked ? '' : 'none';
                        }
                    }
                });
            });
        });

        // 1. Copiar al Portapapeles (Respetando filas visibles)
        function copiarPortapapeles() {
            let texto = "";
            let rows = document.querySelectorAll("#repositorioTable tr");
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    let cols = row.querySelectorAll("th, td");
                    let rowData = [];
                    for (let i = 0; i < cols.length - 1; i++) {
                        if (cols[i].style.display !== 'none') {
                            rowData.push(cols[i].innerText.trim());
                        }
                    }
                    texto += rowData.join("\t") + "\n";
                }
            });
            navigator.clipboard.writeText(texto).then(() => {
                alert("¡Datos copiados al portapapeles exitosamente!");
            });
        }

        // 2. Exportar a Excel (CSV)
        function exportarExcel() {
            let csv = [];
            let rows = document.querySelectorAll("#repositorioTable tr");
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
                    csv.push(data.join(","));
                }
            });

            let csvFile = new Blob(["\uFEFF" + csv.join("\n")], {
                type: "text/csv;charset=utf-8;"
            });
            let downloadLink = document.createElement("a");
            downloadLink.download = "Repositorio_Trabajos_Grado.csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        // 3. Exportar a PDF utilizando PDFMake
        function exportarPDF() {
            let bodyData = [];
            let rows = document.querySelectorAll("#repositorioTable tr");

            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    let cols = row.querySelectorAll("th, td");
                    let rowData = [];
                    for (let i = 0; i < cols.length - 1; i++) {
                        if (cols[i].style.display !== 'none') {
                            rowData.push({
                                text: cols[i].innerText.trim(),
                                fontSize: 9
                            });
                        }
                    }
                    bodyData.push(rowData);
                }
            });

            let docDefinition = {
                pageOrientation: 'landscape',
                content: [{
                        text: 'Repositorio Institucional de Trabajos de Grado',
                        fontSize: 16,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    },
                    {
                        table: {
                            headerRows: 1,
                            widths: ['*', '*', '*', '*', '*'],
                            body: bodyData
                        }
                    }
                ],
                defaultStyle: {
                    font: 'Roboto'
                }
            };

            pdfMake.createPdf(docDefinition).download('Repositorio_Trabajos_Grado.pdf');
        }
    </script>
@stop
