@php
    use App\Traits\RoleTrait;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title') | Bookings</title>
	<meta name="description" content="Caribbean Transfers - Bookings">
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="/assets/img/icons/icon-48x48.png">

    <link href="{{ mix('/assets/css/base/fonts.min.css') }}?family=Inter:wght@300;400;600&display=swap" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/base/fonts.min.css') }}?family=Inter:wght@300;400;600&display=swap" rel="stylesheet" >
    <link href="{{ mix('/assets/css/base/base.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/base/base.min.css') }}" rel="stylesheet" >

    @stack('up-stack')
</head>
<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
    <div class="wrapper">
        <nav id="sidebar" class="sidebar js-sidebar">
            <div class="sidebar-content js-simplebar">
                <a class="sidebar-brand" href="/">
                    <span class="sidebar-brand-text align-middle">
                        Caribbean Transfers
                        <sup><small class="badge bg-primary text-uppercase">Bookings</small></sup>
                    </span>
                    <svg class="sidebar-brand-icon align-middle" width="32px" height="32px" viewbox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="square" stroke-linejoin="miter" color="#FFFFFF" style="margin-left: -3px">
                        <path d="M12 4L20 8.00004L12 12L4 8.00004L12 4Z"></path>
                        <path d="M20 12L12 16L4 12"></path>
                        <path d="M20 16L12 20L4 16"></path>
                    </svg>
                </a>
    
                <div class="sidebar-user">
                    <div class="d-flex justify-content-center">
                        <div class="flex-shrink-0">
                            <img src="/assets/img/logos/brand.svg" class="avatar img-fluid rounded me-1" alt="{{ auth()->user()->name }}">
                        </div>
                        <div class="flex-grow-1 ps-2">
                            <a class="sidebar-user-title dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ auth()->user()->name }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-start">                                
                                <a class="dropdown-item" href="{{ route('logout') }}">Cerrar Sesión</a>
                            </div>
    
                            <div class="sidebar-user-subtitle">Caribbean Transfers</div>
                        </div>
                    </div>
                </div>
    
                <ul class="sidebar-nav">
                    <li class="sidebar-header">
                        Módulos
                    </li>

                    @if(RoleTrait::hasPermission(42))
                        <li class="sidebar-item">
                            <a href="#dashboard" data-bs-toggle="collapse" class="sidebar-link collapsed">
                                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboards</span>
                            </a>
                            <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                                @if(RoleTrait::hasPermission(42))
                                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('dashboard.admin') }}">Admin</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(RoleTrait::hasPermission(43))
                        <li class="sidebar-item">
                            <a href="#dashboard" data-bs-toggle="collapse" class="sidebar-link collapsed">
                                <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Reportes</span>
                            </a>
                            <ul id="dashboard" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                                @if(RoleTrait::hasPermission(43))
                                    <li class="sidebar-item"><a class="sidebar-link" href="{{ route('reports.payment') }}">Pagos</a></li>
                                @endif
                            </ul>
                        </li>
                    @endif

                    @if(RoleTrait::hasPermission(26))
                    <li class="sidebar-item @if(request()->is('/tpv/handler')) active @endif">
                        <a class="sidebar-link" href="/tpv/handler">
                            <i class="align-middle" data-feather="shopping-cart"></i> <span class="align-middle">TPV</span>
                        </a>
                    </li>
                    @endif

                    @if(RoleTrait::hasPermission(36) || RoleTrait::hasPermission(37) || RoleTrait::hasPermission(39) )
                    <li class="sidebar-item">
                        <a href="#operation" data-bs-toggle="collapse" class="sidebar-link collapsed">
                            <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Operación</span>
                        </a>
                        <ul id="operation" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            @if(RoleTrait::hasPermission(36))
                                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('operation.index') }}">Descargar</a></li>                           
                            @endif
                            @if(RoleTrait::hasPermission(37))
                                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('operation.managment') }}">Gestión</a></li>
                            @endif
                            @if(RoleTrait::hasPermission(39))
                                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('operation.confirmation') }}">Confirmaciones</a></li>
                            @endif
                        </ul>
                    </li>
                    @endif

                    @if(RoleTrait::hasPermission(10))
                    <li class="sidebar-item @if(request()->is('reservations')) active @endif">
                        <a class="sidebar-link" href="{{ route('reservations.index') }}">
                            <i class="align-middle" data-feather="calendar"></i> <span class="align-middle">Reservaciones</span>
                        </a>
                    </li>
                    @endif
                    
                    @if(RoleTrait::hasPermission(28) || RoleTrait::hasPermission(32))
                    <li class="sidebar-item">
                        <a href="#configs" data-bs-toggle="collapse" class="sidebar-link collapsed">
                            <i class="align-middle" data-feather="database"></i> <span class="align-middle">Configuraciones</span>
                        </a>
                        <ul id="configs" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                            @if(RoleTrait::hasPermission(28))
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ route('config.zones') }}">Zonas</a></li>
                            @endif
                            @if(RoleTrait::hasPermission(32))
                            <li class="sidebar-item"><a class="sidebar-link" href="{{ route('config.ratesDestination') }}">Tarifas</a></li>
                            @endif
                        </ul>
                    </li>                    
                    @endif

                    @if(RoleTrait::hasPermission(1) || RoleTrait::hasPermission(6))
                    <li class="sidebar-item @if(request()->is('users') || request()->is('roles')) active @endif">
                        <a href="#auth" data-bs-toggle="collapse" class="sidebar-link collapsed">
                            <i class="align-middle" data-feather="users"></i> <span class="align-middle">Administración</span>
                        </a>
                        <ul id="auth" class="sidebar-dropdown list-unstyled collapse @if(request()->is('users') || request()->is('roles')) show @endif" data-bs-parent="#sidebar">
                            @if(RoleTrait::hasPermission(1))
                                <li class="sidebar-item @if(request()->is('users')) active @endif"><a class="sidebar-link" href="{{ route('users.index') }}">Usuarios</a></li>
                            @endif
                            @if(RoleTrait::hasPermission(6))
                                <li class="sidebar-item"><a class="sidebar-link" href="{{ route('roles.index') }}">Roles</a></li>
                            @endif
                        </ul>
                    </li>    
                    @endif
                </ul>
            </div>
        </nav>
    
        <div class="main">
            <nav class="navbar navbar-expand navbar-light navbar-bg">
                <a class="sidebar-toggle js-sidebar-toggle">
                    <i class="hamburger align-self-center"></i>
                </a>
                @if(RoleTrait::hasPermission(27))
                <form class="d-none d-sm-inline-block">
                    <div class="input-group input-group-navbar">
                        <input type="text" class="form-control" placeholder="Buscar..." aria-label="Busqueda">
                        <button class="btn" type="button">
                            <i class="align-middle" data-feather="search"></i>
                        </button>
                    </div>
                </form>
                @endif
                <div class="navbar-collapse collapse">
                    <ul class="navbar-nav navbar-align">                                           
                        <li class="nav-item">
                            <a class="nav-icon js-fullscreen d-none d-lg-block" href="#">
                                <div class="position-relative">
                                    <i class="align-middle" data-feather="maximize"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-icon pe-md-0 dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <img src="/assets/img/logos/brand.svg" class="avatar img-fluid rounded" alt="Caribbean">
                            </a>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="{{ route('logout') }}">Cerrar Sesión</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
    
            <main class="content">
                @yield('content')
            </main>
    
            <footer class="footer">
                <div class="container-fluid">
                    <div class="row text-muted">
                        <div class="col-6 text-start">
                            <p class="mb-0">
                                <strong>Bookings | Caribbean Transfers</strong> &copy;
                            </p>
                        </div>
                        <div class="col-6 text-end">
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <a class="text-muted" href="mailto:development@caribbean-transfers.com">Support</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>        
    </div>    
    
    <script src="{{ mix('/assets/js/base.min.js') }}"></script>
    <script src="{{ mix('assets/js/sweetalert2.js') }}"></script>
    
    @stack('bootom-stack')
</body>
</html>