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
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1><i class="bi bi-people-fill"></i> Gestión de Usuarios</h1>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex flex-wrap justify-content-between align-items-center py-2">
            <div>
                <a href="{{ route('admin.usuarios.roles.papelera') }}" class="btn btn-outline-light btn-sm shadow-sm me-2">
                    <i class="bi bi-trash"></i> Ver Papelera de Roles
                </a>
                <span class="font-weight-bold d-none d-md-inline"><i class="bi bi-table me-1"></i> Listado General de
                    Usuarios</span>
            </div>

            <!-- Botones de Exportación y Buscador -->
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
                                    data-column="0" checked> #</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="1" checked> Nombre Completo</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="2" checked> C.I.</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="3" checked> Correo Electrónico</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="4" checked> Roles y Gestión</label></li>
                        <li><label class="dropdown-item cursor-pointer mb-1"><input type="checkbox" class="col-toggle me-2"
                                    data-column="5" checked> Estado</label></li>
                    </ul>
                </div>

                <!-- Buscador General -->
                <div style="width: 200px;">
                    <input type="text" id="buscador-general" class="form-control form-control-sm"
                        placeholder="Buscar usuario...">
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="tabla-usuarios"
                    class="table table-bordered table-striped table-hover mb-0 align-middle text-nowrap">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Nombre Completo</th>
                            <th>C.I.</th>
                            <th>Correo Electrónico</th>
                            <th>Roles y Gestión</th>
                            <th class="text-center" style="width: 15%">Estado</th>
                            <th class="text-center" style="width: 20%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($usuarios as $index => $usuario)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="text-wrap"><strong>{{ $usuario->paterno }} {{ $usuario->materno }}
                                        {{ $usuario->nombres }}</strong></td>
                                <td>{{ $usuario->ci }}</td>
                                <td>{{ $usuario->email }}</td>
                                <td class="text-wrap">
                                    @forelse($usuario->rolesPorGestion as $asignacion)
                                        <span class="badge bg-info text-dark mb-1">
                                            {{ $asignacion->role->name ?? 'Sin Rol' }}
                                            <small>({{ $asignacion->gestion->anio ?? '' }} -
                                                {{ $asignacion->gestion->periodo ?? '' }})</small>
                                        </span><br>
                                    @empty
                                        <span class="text-muted font-italic">Sin roles asignados</span>
                                    @endforelse
                                </td>
                                <td class="text-center">
                                    @if ($usuario->activo)
                                        <span class="badge bg-success text-white"><i class="bi bi-check-circle"></i>
                                            Habilitado</span>
                                    @else
                                        <span class="badge bg-warning text-dark"><i class="bi bi-clock-history"></i>
                                            Pendiente</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 5px;">
                                        <form action="{{ route('admin.usuarios.toggle', $usuario->id) }}" method="POST"
                                            style="margin: 0;">
                                            @csrf
                                            @method('PATCH')
                                            @if ($usuario->activo)
                                                <button type="submit" class="btn btn-sm btn-danger shadow-sm"
                                                    title="Inhabilitar acceso">
                                                    <i class="bi bi-person-dash-fill"></i> Inhabilitar
                                                </button>
                                            @else
                                                <button type="submit" class="btn btn-sm btn-success shadow-sm"
                                                    title="Habilitar acceso">
                                                    <i class="bi bi-person-check-fill"></i> Habilitar
                                                </button>
                                            @endif
                                        </form>
                                        <a href="{{ route('admin.usuarios.asignar.rol', $usuario->id) }}"
                                            class="btn btn-warning btn-sm text-dark shadow-sm"
                                            title="Asignar Roles por Gestión">
                                            <i class="bi bi-person-badge-fill"></i> Roles
                                        </a>
                                    </div>
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
    <!-- Librerías de exportación directas (PDFMake y VFS Fonts) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscadorGeneral = document.getElementById('buscador-general');
            const filas = document.querySelectorAll('#tabla-usuarios tbody tr');

            // Buscador en tiempo real
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

            // Control de Visibilidad de Columnas (Colvis nativo)
            document.querySelectorAll('.col-toggle').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const colIndex = parseInt(this.getAttribute('data-column'));
                    const isChecked = this.checked;

                    const table = document.getElementById('tabla-usuarios');
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
            let rows = document.querySelectorAll("#tabla-usuarios tr");
            rows.forEach(row => {
                if (row.style.display !== 'none') {
                    let cols = row.querySelectorAll("th, td");
                    let rowData = [];
                    for (let i = 0; i < cols.length - 1; i++) {
                        if (cols[i].style.display !== 'none') {
                            rowData.push(cols[i].innerText.replace(/(\r\n|\n|\r)/gm, " ").trim());
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
            let rows = document.querySelectorAll("#tabla-usuarios tr");
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
            downloadLink.download = "Gestion_Usuarios.csv";
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        // 3. Exportar a PDF utilizando PDFMake
        function exportarPDF() {
            let bodyData = [];
            let rows = document.querySelectorAll("#tabla-usuarios tr");

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
                    bodyData.push(rowData);
                }
            });

            let docDefinition = {
                pageOrientation: 'landscape',
                content: [{
                        text: 'Listado General de Usuarios',
                        fontSize: 16,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    },
                    {
                        table: {
                            headerRows: 1,
                            widths: ['5%', '*', '15%', '20%', '20%', '15%'],
                            body: bodyData
                        }
                    }
                ],
                defaultStyle: {
                    font: 'Roboto'
                }
            };

            pdfMake.createPdf(docDefinition).download('Gestion_Usuarios.pdf');
        }
    </script>

    @include('admin.alertas')
@stop
