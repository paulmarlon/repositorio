@extends('adminlte::page')

@section('title', 'Mi Perfil')

@section('content_header')
    <div class="container-fluid">
        <h1><i class="bi bi-person-badge-fill mr-2"></i> Mi Perfil</h1>
    </div>
@stop

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-primary card-outline shadow-sm">
                <div class="card-header bg-white">
                    <h3 class="card-title font-weight-bold text-primary">
                        <i class="bi bi-gear-fill mr-1"></i> Actualizar información personal
                    </h3>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                        <i class="bi bi-check-circle-fill mr-1"></i> {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <!-- Vista previa o foto actual -->
                        <div class="form-group text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <img id="avatar-preview"
                                    src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->nombres) . '&background=0D8ABC&color=fff&size=160' }}"
                                    class="img-circle elevation-3 border border-primary" width="120" height="120"
                                    alt="Avatar" style="object-fit: cover;">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="avatar"><i class="bi bi-camera-fill mr-1"></i> Cambiar foto de perfil</label>
                            <div class="custom-file">
                                <input type="file" name="avatar"
                                    class="form-control-file @error('avatar') is-invalid @enderror" id="avatar"
                                    accept="image/*">
                            </div>
                            @error('avatar')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nombres"><i class="bi bi-person mr-1"></i> Nombres</label>
                            <input type="text" name="nombres" class="form-control"
                                value="{{ old('nombres', $user->nombres) }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="paterno"><i class="bi bi-person-dash mr-1"></i> Apellido Paterno</label>
                                    <input type="text" name="paterno" class="form-control"
                                        value="{{ old('paterno', $user->paterno) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="materno"><i class="bi bi-person-dash mr-1"></i> Apellido Materno</label>
                                    <input type="text" name="materno" class="form-control"
                                        value="{{ old('materno', $user->materno) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-light text-right">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save2 mr-1"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        document.getElementById('avatar').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@stop
