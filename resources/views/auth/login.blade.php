@extends('adminlte::auth.login')

@section('adminlte_js')
    @parent
    @if (session('mensaje'))
        <script>
            Swal.fire({
                title: 'Aviso de Registro',
                text: "{{ session('mensaje') }}",
                icon: "{{ session('icon', 'info') }}",
                confirmButtonText: 'Entendido'
            });
        </script>
    @endif
@endsection
