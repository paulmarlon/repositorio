@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    $loginUrl = $layoutHelper->makeUrl($loginUrl);
    $registerUrl = $layoutHelper->makeUrl($registerUrl);
    $passResetUrl = $layoutHelper->makeUrl($passResetUrl);
@endphp

{{-- Cabecera traducida --}}
@section('auth_header', 'Ingrese sus credenciales para iniciar sesión')

@section('auth_body')
    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Email field --}}
        <label for="email" class="visually-hidden">Correo Electrónico</label>

        <div class="input-group mb-3">
            {{-- 1. Movimos el contenedor del icono a la IZQUIERDA --}}
            <div class="input-group-text">
                <span class="bi bi-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>

            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="Correo Electrónico" autofocus>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <label for="password" class="visually-hidden">Contraseña</label>

        <div class="input-group mb-3">
            {{-- 1. Movimos el contenedor del icono a la IZQUIERDA --}}
            <div class="input-group-text">
                <span class="bi bi-lock-fill {{ config('adminlte.classes_auth_icon', '') }}"></span>
            </div>

            <input type="password" name="password" id="password"
                class="form-control @error('password') is-invalid @enderror" placeholder="Contraseña">

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="form-check" title="Recordar sesión en este equipo">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        Recuerdame
                    </label>
                </div>
            </div>

            <div class="col-5">
                <div class="d-grid">
                    <button type="submit" class="btn {{ config('adminlte.classes_auth_btn', 'btn-primary') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i>
                        Ingresar
                    </button>
                </div>
            </div>
        </div>
    </form>
    @include('adminlte::auth.social-links', ['fallbackText' => 'Ingresar'])
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if ($passResetUrl)
        <p class="my-0">
            <a href="{{ $passResetUrl }}">
                Olvidé mi contraseña
            </a>
        </p>
    @endif

    {{-- Register link --}}
    @if ($registerUrl)
        <p class="my-0">
            <a href="{{ $registerUrl }}">
                Registrar una nueva membresía
            </a>
        </p>
    @endif
@stop
