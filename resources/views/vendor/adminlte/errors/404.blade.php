@extends('adminlte::errors.error-page', [
    'errorCode' => '404',
    'errorTheme' => 'primary',
    'errorTitle' => __('adminlte::adminlte.error_not_found_title'),
    'errorMessage' => __('adminlte::adminlte.error_not_found_message'),
])

@section('adminlte_css')
    @parent
    <style>
        .error-institution-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .error-institution-brand img {
            max-height: 45px;
            object-fit: contain;
        }

        .error-institution-brand span {
            font-size: 1.1rem;
            font-weight: 600;
            color: #6c757d;
        }
    </style>
@endsection

@section('content_top')
    <div class="text-center mb-3">
        <div class="error-institution-brand">
            @if (isset($instituto) && $instituto->logo)
                <img src="{{ asset('storage/' . $instituto->logo) }}" alt="Logo Institucional">
            @else
                <i class="bi bi-compass-fill text-primary fs-3"></i>
            @endif

            <span>
                {{ isset($instituto) && $instituto->nombre_instituto ? $instituto->nombre_instituto : config('app.name', 'Sistema Académico') }}
            </span>
        </div>
    </div>
@endsection

@section('error_footer')
    <div class="text-center mt-4">
        <a href="{{ url('/') }}" class="btn btn-primary shadow-sm px-4">
            <i class="bi bi-house-door-fill me-1"></i> Volver al Inicio
        </a>
    </div>
@endsection
