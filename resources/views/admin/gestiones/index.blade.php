@extends('adminlte::page')
@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <h1><b>Listado de gestiones académicas</b></h1>
    <hr>
    <a href="{{ route('admin.gestiones.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Crear nueva gestión
    </a>
    <a href="{{ route('admin.gestiones.index') }}" class="btn btn-default">
        <i class="bi bi-check2-circle"></i> Ver activos
    </a>
    <a href="{{ route('admin.gestiones.papelera') }}" class="btn btn-warning">
        <i class="bi bi-trash"></i> Ver papelera
    </a>
    <hr>
@stop

@section('content')
    <div class="row">
        @foreach ($gestiones as $gestion)
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box zoomP">
                    <span class="info-box-icon elevation-1" style="background: transparent;">
                        <img src="{{ url('img/calendario.png') }}" width="60px" alt="Calendario">
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">{{ $gestion->periodo }}</span>
                        <span class="info-box-number" style="color:rgb(0, 149, 255); font-size:20px">
                            {{ $gestion->anio }}
                        </span>

                        {{-- Control del campo booleano activo --}}
                        <div class="mb-2">
                            @if ($gestion->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </div>

                        <div class="row mt-1">
                            @if ($gestion->trashed())
                                <div class="col-12">
                                    <form action="{{ route('admin.gestiones.restaurar', $gestion->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info btn-block">
                                            <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="col-12 d-flex">
                                    <a href="{{ route('admin.gestiones.edit', $gestion) }}"
                                        class="btn btn-sm btn-success mr-2" title="Editar">
                                        <i class="bi bi-pencil-square"></i> Editar
                                    </a>
                                    <form action="{{ route('admin.gestiones.destroy', $gestion->id) }}" method="POST"
                                        id="miFormulario{{ $gestion->id }}">
                                        @csrf @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger btn-block"
                                            onclick="confirmarEliminacion({{ $gestion->id }})" title="Enviar a papelera">
                                            <i class="bi bi-trash-fill"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@stop

@section('css')
@stop

@section('js')
    <script>
        function confirmarEliminacion(id) {
            Swal.fire({
                title: '¿Está seguro?',
                text: "Esta gestión pasará a la papelera.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, enviar a papelera',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('miFormulario' + id).submit();
                }
            });
        }
    </script>
    @include('admin.alertas')
@stop
