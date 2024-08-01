    @php
        use App\Traits\RoleTrait;
    @endphp
    @php
        $links = []; //LINKS GENERALES
        $links_dashboard = [];
        $links_reports = [];
        $links_selling_point = [];
        $links_operations = [];
        $links_settings = [];
        $links_administration = [];

        //DASHBOARD
        if(RoleTrait::hasPermission(42) || RoleTrait::hasPermission(62) || RoleTrait::hasPermission(45) || RoleTrait::hasPermission(63)):
            if(RoleTrait::hasPermission(42)):
                $links_dashboard[] = [
                    'name' => 'Ventas Generales',
                    'route' => route('dashboard.sales',['general']),
                    'active' => request()->is('dashboard/sales/general'),
                ];
            endif;
            if(RoleTrait::hasPermission(62)):
                $links_dashboard[] = [
                    'name' => 'Ventas en Linea',
                    'route' => route('dashboard.sales',['online']),
                    'active' => request()->is('dashboard/sales/online'),
                ];
            endif;
            if(RoleTrait::hasPermission(63)):
                $links_dashboard[] = [
                    'name' => 'Ventas de Aereopuerto',
                    'route' => route('dashboard.sales',['airport']),
                    'active' => request()->is('dashboard/sales/airport'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'dashboard',
                'name' => 'Dashboards',
                'route' => null,
                'active' => request()->routeIs('dashboard.*'),
                'urls' => $links_dashboard
            ]);
        endif;

        //REPORTES
        if(RoleTrait::hasPermission(43) || RoleTrait::hasPermission(44) || RoleTrait::hasPermission(45) || RoleTrait::hasPermission(50)):
            if(RoleTrait::hasPermission(43)):
                $links_reports[] = [
                    'name' => 'Pagos',
                    'route' => route('reports.payment'),
                    'active' => request()->routeIs('reports.payment','reports.payment.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(44)):
                $links_reports[] = [
                    'name' => 'Ventas',
                    'route' => route('reports.sales'),
                    'active' => request()->routeIs('reports.sales','reports.sales.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(45)):
                $links_reports[] = [
                    'name' => 'Comisiones',
                    'route' => route('reports.commissions'),
                    'active' => request()->routeIs('reports.commissions','reports.commissions.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(50)):
                $links_reports[] = [
                    'name' => 'Efectivo',
                    'route' => route('reports.cash'),
                    'active' => request()->routeIs('reports.cash','reports.cash.action'),
                ];
            endif;            
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'reports',
                'name' => 'Reportes',
                'route' => null,
                'active' => request()->routeIs('reports.payment','reports.payment.action','reports.sales','reports.sales.action','reports.commissions','reports.commissions.action','reports.cash','reports.cash.action'),
                'urls' => $links_reports
            ]);
        endif;

        //TPV
        if(RoleTrait::hasPermission(26)):
            array_push($links,[
                'type' => 'single',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>',
                'code' => 'tpv',
                'name' => 'TPV',
                'route' => route('tpv.handler'),
                'active' => request()->routeIs('tpv.handler')
            ]);
        endif;

        //PUNTO DE VENTA
        if(RoleTrait::hasPermission(51) || RoleTrait::hasPermission(91) || RoleTrait::hasPermission(52) || RoleTrait::hasPermission(54) ):
            if(RoleTrait::hasPermission(51)):
                $links_selling_point[] = [
                    'name' => 'Ventas',
                    'route' => route('pos.index'),
                    'active' => request()->routeIs('pos.index','pos.index.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(91)):
                $links_selling_point[] = [
                    'name' => 'Ventas generales',
                    'route' => route('pos.generals.index'),
                    'active' => request()->routeIs('pos.generals.index','pos.generals.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(52)):
                $links_selling_point[] = [
                    'name' => 'Capturar venta',
                    'route' => route('pos.capture'),
                    'active' => request()->routeIs('pos.capture'),
                ];
            endif;
            if(RoleTrait::hasPermission(54)):
                $links_selling_point[] = [
                    'name' => 'Vendedores',
                    'route' => route('pos.vendors'),
                    'active' => request()->routeIs('pos.vendors'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'selling_point',
                'name' => 'Punto de venta',
                'route' => null,
                'active' => request()->routeIs('pos.*'),
                'urls' => $links_selling_point
            ]);
        endif;

        //OPERACION
        if(RoleTrait::hasPermission(36) || RoleTrait::hasPermission(37) || RoleTrait::hasPermission(39) || RoleTrait::hasPermission(46) || RoleTrait::hasPermission(47) || RoleTrait::hasPermission(76) || RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) ):
            if(RoleTrait::hasPermission(36)):
                $links_operations[] = [
                    'name' => 'Descargar',
                    'route' => route('operation.download'),
                    'active' => request()->routeIs('operation.download'),
                ];
            endif;
            if(RoleTrait::hasPermission(37)):
                $links_operations[] = [
                    'name' => 'Gestión',
                    'route' => route('operation.managment'),
                    'active' => request()->routeIs('operation.managment','operation.managment.search'),
                ];
            endif;
            if(RoleTrait::hasPermission(39)):
                $links_operations[] = [
                    'name' => 'Confirmaciones',
                    'route' => route('operation.confirmation'),
                    'active' => request()->routeIs('operation.confirmation','operation.confirmation.search'),
                ];
            endif;
            if(RoleTrait::hasPermission(46)):
                $links_operations[] = [
                    'name' => 'CC Form',
                    'route' => route('operation.ccform'),
                    'active' => request()->routeIs('operation.ccform'),
                ];
            endif;
            if(RoleTrait::hasPermission(47)):
                $links_operations[] = [
                    'name' => 'SPAM',
                    'route' => route('operation.spam'),
                    'active' => request()->routeIs('operation.spam','operation.spam.search'),
                ];
            endif;
            if(RoleTrait::hasPermission(76) || RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79)):
                $links_operations[] = [
                    'name' => 'Operaciones',
                    'route' => route('operation.index'),
                    'active' => request()->routeIs('operation.index','operation.index.search'),
                ];
            endif;            
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'operations',
                'name' => 'Operación',
                'route' => null,
                'active' => request()->routeIs('operation.*'),
                'urls' => $links_operations
            ]);
        endif;

        //RESERVACIONES
        if(RoleTrait::hasPermission(10)):
            array_push($links,[
                'type' => 'single',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>',
                'code' => 'bookings',
                'name' => 'Reservaciones',
                'route' => route('reservations.index'),
                'active' => request()->routeIs('reservations.index','reservations.search')
            ]);        
        endif;

        //CONFIGURACIONES
        if(RoleTrait::hasPermission(28) || RoleTrait::hasPermission(32)):
            if(RoleTrait::hasPermission(28)):
                $links_settings[] = [
                    'name' => 'Zonas',
                    'route' => route('config.zones'),
                    'active' => request()->routeIs('config.zones','config.zones.getZones'),
                ];
            endif;
            if(RoleTrait::hasPermission(32)):
                $links_settings[] = [
                    'name' => 'Tarifas',
                    'route' => route('config.ratesDestination'),
                    'active' => request()->routeIs('config.ratesDestination','config.ratesZones'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'settings',
                'name' => 'Configuraciones',
                'route' => null,
                'active' => request()->routeIs('config.zones','config.zones.getZones','config.ratesDestination','config.ratesZones'),
                'urls' => $links_settings
            ]);
        endif;

        //ADMINISTRACION
        if(RoleTrait::hasPermission(1) || RoleTrait::hasPermission(6) || RoleTrait::hasPermission(73) || RoleTrait::hasPermission(74) || RoleTrait::hasPermission(75)):
            if(RoleTrait::hasPermission(1)):
                $links_administration[] = [
                    'name' => 'Usuarios',
                    'route' => route('users.index'),
                    'active' => request()->routeIs('users.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(6)):
                $links_administration[] = [
                    'name' => 'Roles',
                    'route' => route('roles.index'),
                    'active' => request()->routeIs('roles.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(73)):
                $links_administration[] = [
                    'name' => 'Empresas',
                    'route' => route('enterprises.index'),
                    'active' => request()->routeIs('enterprises.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(74)):
                $links_administration[] = [
                    'name' => 'Vehiculos',
                    'route' => route('vehicles.index'),
                    'active' => request()->routeIs('vehicles.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(75)):
                $links_administration[] = [
                    'name' => 'Conductores',
                    'route' => route('drivers.index'),
                    'active' => request()->routeIs('drivers.*'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'administration',
                'name' => 'Administración',
                'route' => null,
                'active' => request()->routeIs('users.*','roles.*','enterprises.*','vehicles.*','drivers.*'),
                'urls' => $links_administration
            ]);
        endif;
    @endphp
    <!--  BEGIN SIDEBAR  -->
    <div class="sidebar-wrapper sidebar-theme">
        <nav id="sidebar">
            <div class="navbar-nav theme-brand flex-row  text-center">
                <div class="nav-logo">
                    <div class="nav-item theme-logo">
                        <a href="/">
                            <img src="{{ asset('/assets/img/logos/brand.svg') }}" class="navbar-logo" alt="logo">
                        </a>
                    </div>
                    <div class="nav-item theme-text">
                        <a href="/" class="nav-link"> AFILIADOS </a>
                    </div>
                </div>
                <div class="nav-item sidebar-toggle">
                    <div class="btn-toggle sidebarCollapse">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevrons-left"><polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline></svg>
                    </div>
                </div>
            </div>
            <ul class="list-unstyled menu-categories" id="accordionExample">
                @foreach ($links as $link)
                    @if ( $link['type'] == 'single' )
                        <li class="menu <?=( isset($link['active']) && $link['active'] ? 'active' : '' )?>">
                            <a href="{{ $link['route'] }}" aria-expanded="<?=( isset($link['active']) && $link['active'] ? 'true' : 'false' )?>" class="dropdown-toggle">
                                <div class="">
                                    <?=strval($link['icon'])?>
                                    <span>{{ $link['name'] }}</span>
                                </div>
                            </a>
                        </li>  
                    @else
                        <li class="menu <?=( $link['active'] ? 'active' : '' )?>">
                            <a href="#{{ $link['code'] }}" data-bs-toggle="collapse" aria-expanded="<?=( $link['active'] ? true : false )?>" class="dropdown-toggle">
                                <div class="">
                                    <?=strval($link['icon'])?>
                                    <span>{{ $link['name'] }}</span>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-right"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </div>
                            </a>
                            <ul class="collapse submenu list-unstyled <?=( $link['active'] ? 'show' : '' )?>" id="{{ $link['code'] }}" data-bs-parent="#accordionExample">
                                @if ( isset($link['urls']) )
                                    @foreach ($link['urls'] as $url)
                                    <li class="<?=( $url['active'] ? 'active' : '' )?>">
                                        <a href="{{ $url['route'] }}"> {{ $url['name'] }} </a>
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    </div>
    <!--  END SIDEBAR  -->