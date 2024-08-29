<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Bookings</title>
	<meta name="description" content="Caribbean Transfers | Affiliates">
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="/assets/img/icons/favicon-32x32.png">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700&display=swap" rel="stylesheet">
    <link href="/assets/css/core/core.min.css" rel="preload" as="style" >
    <link href="/assets/css/core/core.min.css" rel="stylesheet" >
    <link href="/assets/css/panel/panel.min.css" rel="preload" as="style" >
    <link href="/assets/css/panel/panel.min.css" rel="stylesheet" >
    <link href="/assets/css/panel/panel2.min.css" rel="preload" as="style" >
    <link href="/assets/css/panel/panel2.min.css" rel="stylesheet" >

    @stack('Css')
</head> 
<body class="layout-boxed">

    @include('layout.partials.loader')

    <!--  BEGIN MAIN CONTAINER  -->
    <div class="main-container px-3" id="container">

        <div class="overlay"></div>
        <div class="search-overlay"></div>

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content ms-0 mt-5">
            <div class="layout-px-spacing" style="padding: 0 !important;">

                <div class="middle-content">

                    <!--  BEGIN BREADCRUMBS  -->
                    <div class="secondary-nav">
                        <div class="breadcrumbs-container" data-page-heading="Analytics">
                            <header class="header navbar navbar-expand-sm px-2 justify-content-between">
                                <div>
                                    <a href="{{route('dashboard') }}" class="btn btn-primary" >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        <span>Inicio</span>
                                    </a>
    
                                    <a href="{{ route('dashboard.sales',['general']) }}" class="btn btn-{{ request()->is('dashboard/sales/general') ? 'success' : 'primary' }}" >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        <span>Ventas generales</span>
                                    </a>
                                    <a href="{{ route('dashboard.sales',['online']) }}" class="btn btn-{{ request()->is('dashboard/sales/online') ? 'success' : 'primary' }}" >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        <span>Ventas en linea</span>
                                    </a>
                                    <a href="{{ route('dashboard.sales',['airport']) }}" class="btn btn-{{ request()->is('dashboard/sales/airport') ? 'success' : 'primary' }}" >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                                        <span>Ventas de Aereopuerto</span>
                                    </a>
                                </div>
                                <div>
                                    <a href="{{ route('logout') }}" class="btn btn-primary __logouts">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> 
                                        <span>Cerrar Sesión</span>
                                    </a>
                                </div>
                            </header>
                        </div>
                    </div>
                    <!--  END BREADCRUMBS  -->                 
                    
                    <div class="row layout-top-spacing">

                        @yield('content')

                    </div>

                </div>

            </div>

        </div>
        <!--  END CONTENT AREA  -->

    </div>
    <!-- END MAIN CONTAINER -->

    <script src="{{ mix('/assets/js/core/core.min.js') }}"></script>
    <script src="{{ mix('/assets/js/panel/panel_custom.min.js') }}"></script>

    @stack('Js')
</body>
</html>