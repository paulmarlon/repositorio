@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('adminlte_css_pre')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@stop

@section('auth_body')
    <div class="login-logo">
        <a href="{{ url('/') }}"><b>{{ config('app.name', 'Laravel') }}</b></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Bienvenido al sistema</p>

            <div class="text-center mb-4">
                <p class="text-muted">Gestionado con Laravel, PostgreSQL y AdminLTE.</p>
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
