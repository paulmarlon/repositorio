@extends('adminlte::page')
@section('title', $instituto->nombre_instituto ?? 'Sistema')

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $instituto->logo) }}">
    @endif
@endsection

@section('content_header')
    <h1><b>Papelera de gestiones académicas</b></h1>
    <hr>
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
        @forelse ($gestiones as $gestion)
            <div class="col-md-3 col-sm-6 col-12">
                <div class="info-box zoomP">
                    <span class="info-box-icon elevation-1" style="background: transparent;">
                        <img src="{{ url('img/calendario.png') }}" width="60px" alt="Calendario">
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text text-bold">{{ $gestion->periodo }}</span>
                        <span class="info-box-number" style="color:rgb(150, 150, 150); font-size:20px">
                            {{ $gestion->anio }}
                        </span>

                        <div class="mb-2">
                            <span class="badge badge-danger">Eliminado</span>
                        </div>

                        <div class="row mt-1">
                            <div class="col-12 d-flex">
                                <form action="{{ route('admin.gestiones.restaurar', $gestion->id) }}" method="POST"
                                    class="w-100">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info btn-block">
                                        <i class="bi bi-arrow-counterclockwise"></i> Restaurar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    No hay elementos en la papelera.
                </div>
            </div>
        @endforelse
    </div>
    @include('admin.alertas')
@stop
