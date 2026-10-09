@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        <!-- Nombres -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-person"></span>
                </div>
            </div>
            <input type="text" name="nombres" class="form-control @error('nombres') is-invalid @enderror"
                value="{{ old('nombres') }}" placeholder="Nombres" required autofocus>
            @error('nombres')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Apellido Paterno -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-person"></span>
                </div>
            </div>
            <input type="text" name="paterno" class="form-control @error('paterno') is-invalid @enderror"
                value="{{ old('paterno') }}" placeholder="Apellido Paterno">
            @error('paterno')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Apellido Materno -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-person"></span>
                </div>
            </div>
            <input type="text" name="materno" class="form-control @error('materno') is-invalid @enderror"
                value="{{ old('materno') }}" placeholder="Apellido Materno">
            @error('materno')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- CI -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-card-heading"></span>
                </div>
            </div>
            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror"
                value="{{ old('ci') }}" placeholder="Cédula de Identidad (CI)" required>
            @error('ci')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Email -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-envelope"></span>
                </div>
            </div>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="Correo Electrónico" required>
            @error('email')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Password -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-lock"></span>
                </div>
            </div>
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="Contraseña" required>
            @error('password')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-lock-fill"></span>
                </div>
            </div>
            <input type="password" name="password_confirmation" class="form-control" placeholder="Repetir Contraseña"
                required>
        </div>

        <div class="row">
            <div class="col-8">
                <a href="{{ route('login') }}" class="text-center">Ya tengo una cuenta</a>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
            </div>
        </div>
    </form>
@stop
