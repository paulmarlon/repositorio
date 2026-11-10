@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('title', $instituto->nombre_instituto ?? config('app.name', 'Sistema'))

{{-- CABECERA EXTERNA SUPERIOR CONECTADA A SUPABASE S3 --}}
@section('auth_logo')
    <a href="{{ url('/') }}" class="h1 text-decoration-none d-flex align-items-center justify-content-center">
        @if (isset($instituto) && !empty($instituto->logo))
            {{-- Usamos el disco 's3' para apuntar a Supabase --}}
            <img src="{{ Storage::disk('s3')->url($instituto->logo) }}" alt="Logo"
                class="brand-image img-circle elevation-3 mr-2"
                style="width: 50px; height: 50px; object-fit: contain; background: #fff; padding: 2px;">
        @else
            <i class="fas fa-university mr-2"></i>
        @endif
        <span class="font-weight-bold text-dark">{{ $instituto->nombre_instituto ?? config('app.name', 'Sistema') }}</span>
    </a>
@stop

@section('adminlte_css')
    @parent
    @if (isset($instituto) && $instituto->logo)
        <link rel="icon" type="image/png" href="{{ Storage::disk('s3')->url($instituto->logo) }}">
    @endif
@endsection

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_body')
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Bienvenido al sistema</p>

            <div class="text-center mb-4">
                <p class="text-muted">Gestionado con Laravel Sail, PostgreSQL y AdminLTE.</p>
            </div>

            @if (Route::has('login'))
                <div class="social-auth-links text-center mb-3">
                    @auth
                        <a href="{{ url('/home') }}" class="btn btn-block btn-primary">
                            <i class="fas fa-home mr-2"></i> Ir al Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-block btn-primary">
                            <i class="fas fa-sign-in-alt mr-2"></i> Iniciar Sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-block btn-secondary">
                                <i class="fas fa-user-plus mr-2"></i> Registrarse
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </div>
@stop
