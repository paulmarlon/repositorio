@extends('adminlte::page')

@section('title', 'Configuración del Instituto')

@section('css')
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #map {
            height: 300px;
            width: 100%;
            border-radius: 0.375rem;
        }
    </style>
@stop

@section('content_header')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h3 class="mb-0"><i class="bi bi-building me-2"></i> Configuración del Instituto</h3>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card card-primary card-outline shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title text-primary fw-bold mb-0">
                            <i class="bi bi-sliders me-1"></i> Datos Generales y Ubicación
                        </h5>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('configuracion.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <!-- Vista previa del Logo -->
                            <div class="text-center mb-4">
                                <div class="position-relative d-inline-block">
                                    <img id="logo-preview"
                                        src="{{ isset($instituto->logo) ? asset('storage/' . $instituto->logo) : 'https://ui-avatars.com/api/?name=Instituto&background=0D8ABC&color=fff&size=160' }}"
                                        class="img-thumbnail rounded-circle shadow-sm" width="120" height="120"
                                        alt="Logo" style="object-fit: cover;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="logo" class="form-label"><i class="bi bi-image me-1"></i> Logo del
                                    Instituto</label>
                                <input type="file" name="logo"
                                    class="form-control @error('logo') is-invalid @enderror" id="logo"
                                    accept="image/*">
                                @error('logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nombre_instituto" class="form-label"><i class="bi bi-type me-1"></i> Nombre del
                                    Instituto</label>
                                <input type="text" name="nombre_instituto"
                                    class="form-control @error('nombre_instituto') is-invalid @enderror"
                                    value="{{ old('nombre_instituto', $instituto->nombre_instituto ?? '') }}" required>
                                @error('nombre_instituto')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="direccion" class="form-label"><i class="bi bi-geo-alt-fill me-1"></i>
                                    Dirección</label>
                                <input type="text" name="direccion"
                                    class="form-control @error('direccion') is-invalid @enderror"
                                    value="{{ old('direccion', $instituto->direccion ?? '') }}"
                                    placeholder="Ej. Av. Principal #123">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="celular" class="form-label"><i class="bi bi-phone me-1"></i> Celular</label>
                                <input type="text" name="celular"
                                    class="form-control @error('celular') is-invalid @enderror"
                                    value="{{ old('celular', $instituto->celular ?? '') }}" placeholder="Ej. 70000000">
                                @error('celular')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mapa Interactivo -->
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-map me-1"></i> Ubicación GPS (Haz clic en el mapa
                                    para ubicar)</label>
                                <div id="map"></div>
                                <small class="text-muted">También puedes mover el marcador arrastrándolo.</small>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="latitud" class="form-label"><i class="bi bi-compass me-1"></i>
                                        Latitud</label>
                                    <input type="text" id="latitud" name="latitud"
                                        class="form-control @error('latitud') is-invalid @enderror"
                                        value="{{ old('latitud', $instituto->latitud ?? -16.5) }}" readonly>
                                    @error('latitud')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="longitud" class="form-label"><i class="bi bi-compass-fill me-1"></i>
                                        Longitud</label>
                                    <input type="text" id="longitud" name="longitud"
                                        class="form-control @error('longitud') is-invalid @enderror"
                                        value="{{ old('longitud', $instituto->longitud ?? -68.15) }}" readonly>
                                    @error('longitud')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="card-footer bg-light text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save2 me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        // Coordenadas iniciales (por defecto La Paz, Bolivia o las guardadas en BD)
        const latVal = document.getElementById('latitud').value || -16.50000000;
        const lngVal = document.getElementById('longitud').value || -68.15000000;

        // Inicializar el mapa
        const map = L.map('map').setView([latVal, lngVal], 14);

        // Capa base de OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Crear marcador inicial arrastrable
        let marker = L.marker([latVal, lngVal], {
            draggable: true
        }).addTo(map);

        // Función para actualizar los inputs
        function updateInputs(lat, lng) {
            document.getElementById('latitud').value = lat.toFixed(8);
            document.getElementById('longitud').value = lng.toFixed(8);
        }

        // Evento al hacer clic en el mapa
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            marker.setLatLng([lat, lng]);
            updateInputs(lat, lng);
        });

        // Evento al arrastrar el marcador
        marker.on('dragend', function(e) {
            const latLng = marker.getLatLng();
            updateInputs(latLng.lat, latLng.lng);
        });

        // Previsualizar logo
        document.getElementById('logo').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@stop
