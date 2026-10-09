@extends('adminlte::auth.auth-page', [
    'auth_type' => 'register',
    'auth_header' => 'Registro de Usuario',
    'auth_title' => 'Crear una nueva cuenta',
])

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

        <!-- Checkbox Términos de Uso -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="icheck-primary">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Acepto los <a href="javascript:void(0)" id="btnOpenTerms">términos de uso</a>
                    </label>
                </div>
                @error('terms')
                    <span class="text-danger d-block small"><strong>{{ $message }}</strong></span>
                @enderror
            </div>
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

    <!-- Modal de Términos y Condiciones -->
    <div class="modal fade" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Términos y Condiciones de Uso</h5>
                    <button type="button" class="close" id="btnCloseTerms" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-left">
                    <p><strong>1. Aceptación de los Términos</strong></p>
                    <p>Al registrarse en este Repositorio Digital, el usuario se compromete a hacer uso adecuado del sistema
                        y de los recursos académicos institucionales.</p>

                    <p><strong>2. Uso del Contenido</strong></p>
                    <p>Los trabajos académicos, tesis y monografías alojados están protegidos por derechos de autor y se
                        ponen a disposición únicamente con fines de consulta académica e investigación.</p>

                    <p><strong>3. Veracidad de la Información</strong></p>
                    <p>El usuario declara que todos los datos ingresados en el formulario de registro (Nombre, Cédula de
                        Identidad y Correo Electrónico) son fidedignos y personales.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="btnFooterCloseTerms">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('termsModal');
            var btnOpen = document.getElementById('btnOpenTerms');
            var btnClose = document.getElementById('btnCloseTerms');
            var btnFooterClose = document.getElementById('btnFooterCloseTerms');

            function openModal() {
                if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                    $('#termsModal').modal('show');
                } else {
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    document.body.classList.add('modal-open');

                    var backdrop = document.createElement('div');
                    backdrop.id = 'custom-modal-backdrop';
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            }

            function closeModal() {
                if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
                    $('#termsModal').modal('hide');
                } else {
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    var backdrop = document.getElementById('custom-modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            }

            if (btnOpen) btnOpen.addEventListener('click', openModal);
            if (btnClose) btnClose.addEventListener('click', closeModal);
            if (btnFooterClose) btnFooterClose.addEventListener('click', closeModal);
        });
    </script>
@stop
