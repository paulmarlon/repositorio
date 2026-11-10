@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php
    $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home');
    $dashboard_url = $layoutHelper->makeUrl($dashboard_url);

    // Consultamos la configuración del instituto de forma dinámica
    $institutoGlobal = \App\Models\ConfiguracionInstituto::first();

    // Si hay un logo guardado en la base de datos, usamos Storage::disk('public')->url() apuntando a Supabase
    $logoImg =
        isset($institutoGlobal) && $institutoGlobal->logo
            ? Storage::disk('public')->url($institutoGlobal->logo)
            : asset(config('adminlte.logo_img', 'vendor/adminlte/dist/assets/img/AdminLTELogo.png'));

    $logoImgAlt = $institutoGlobal->nombre_instituto ?? config('adminlte.logo_img_alt', 'Admin Logo');
    $logoImgClass = config('adminlte.logo_img_class', 'brand-image opacity-75 shadow');

    // Si hay nombre de instituto, lo usamos; si no, el por defecto de AdminLTE
    $logoText =
        isset($institutoGlobal) && $institutoGlobal->nombre_instituto
            ? '<b>' . e($institutoGlobal->nombre_instituto) . '</b>'
            : config('adminlte.logo', '<b>Admin</b>LTE');

    $brandClass = config('adminlte.classes_brand', '');
    $brandTextClass = config('adminlte.classes_brand_text', 'fw-light');
@endphp

@if ($layoutHelper->isLayoutTopnavEnabled())
    {{-- Navbar Brand (topnav layout) --}}
    <a href="{{ $dashboard_url }}" class="navbar-brand d-flex align-items-center {{ $brandClass }}">

        {{-- Brand logo --}}
        <img src="{{ $logoImg }}" alt="{{ $logoImgAlt }}" width="30" height="30"
            class="{{ $logoImgClass }} me-2" style="object-fit: contain;">

        {{-- Brand text --}}
        <span class="{{ $brandTextClass }}">{!! $logoText !!}</span>

    </a>
@else
    {{-- Sidebar Brand --}}
    <div class="sidebar-brand">
        <a href="{{ $dashboard_url }}" class="brand-link {{ $brandClass }}">

            {{-- Brand logo --}}
            <img src="{{ $logoImg }}" alt="{{ $logoImgAlt }}" class="{{ $logoImgClass }}"
                style="object-fit: contain;">

            {{-- Brand text --}}
            <span class="brand-text {{ $brandTextClass }}">{!! $logoText !!}</span>

        </a>
    </div>
@endif
