@if (session('mensaje'))
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            // Forzar colores limpios independientes del modo de AdminLTE
            background: document.body.classList.contains('dark-mode') ? '#343a40' : '#ffffff',
            color: document.body.classList.contains('dark-mode') ? '#ffffff' : '#495057',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        Toast.fire({
            icon: '{{ session('icon') ?? 'success' }}',
            title: '{{ session('mensaje') }}'
        });
    </script>
@endif
