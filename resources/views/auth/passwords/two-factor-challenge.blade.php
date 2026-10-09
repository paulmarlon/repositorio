@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_body')
    <p class="login-box-msg">Ingresa el código de verificación enviado a tu correo.</p>

    <form action="{{ route('two-factor.verify') }}" method="post">
        @csrf

        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    <span class="bi bi-shield-lock"></span>
                </div>
            </div>
            <input type="text" name="two_factor_code" class="form-control @error('two_factor_code') is-invalid @enderror"
                placeholder="Código de 6 dígitos" required autofocus>
            @error('two_factor_code')
                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block">Verificar</button>
            </div>
        </div>
    </form>
@stop
