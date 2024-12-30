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

        //DASHBOARD
        if(RoleTrait::hasPermission(42) || RoleTrait::hasPermission(62) || RoleTrait::hasPermission(45) || RoleTrait::hasPermission(63)):
            array_push($links,[
                'type' => 'single',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-activity"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>',
                'code' => 'dashbaoard',
                'name' => 'Dashboard',
                'route' => route('dashboard'),
                'active' => request()->routeIs('dashboard')
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

        //REPORTES
        if(RoleTrait::hasPermission(43) || RoleTrait::hasPermission(45) || RoleTrait::hasPermission(50) || RoleTrait::hasPermission(71) || RoleTrait::hasPermission(97) || RoleTrait::hasPermission(98) || RoleTrait::hasPermission(99)):
            // if(RoleTrait::hasPermission(43)):
            //     $links_reports[] = [
            //         'name' => 'Pagos',
            //         'route' => route('reports.payment'),
            //         'active' => request()->routeIs('reports.payment','reports.payment.action'),
            //     ];
            // endif;
            if(RoleTrait::hasPermission(45)):
                $links_reports[] = [
                    'name' => 'Comisiones',
                    'route' => route('reports.commissions'),
                    'active' => request()->routeIs('reports.commissions','reports.commissions.action'),
                ];
            endif;
            // if(RoleTrait::hasPermission(50)):
            //     $links_reports[] = [
            //         'name' => 'Efectivo',
            //         'route' => route('reports.cash'),
            //         'active' => request()->routeIs('reports.cash','reports.cash.action'),
            //     ];
            // endif;
            // if(RoleTrait::hasPermission(71)):
            //     $links_reports[] = [
            //         'name' => 'Cancelaciones',
            //         'route' => route('reports.cancellations'),
            //         'active' => request()->routeIs('reports.cancellations','reports.cancellations.action'),
            //     ];
            // endif;
            if(RoleTrait::hasPermission(98)):
                $links_reports[] = [
                    'name' => 'Ventas',
                    'route' => route('reports.sales'),
                    'active' => request()->routeIs('reports.sales','reports.sales.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(97)):
                $links_reports[] = [
                    'name' => 'Operaciones',
                    'route' => route('reports.operations'),
                    'active' => request()->routeIs('reports.operations','reports.operations.action'),
                ];
            endif;
            if(RoleTrait::hasPermission(99)):
                $links_reports[] = [
                    'name' => 'Conciliacion',
                    'route' => route('reports.conciliation'),
                    'active' => request()->routeIs('reports.conciliation','reports.conciliation.action'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bar-chart"><line x1="12" y1="20" x2="12" y2="10"></line><line x1="18" y1="20" x2="18" y2="4"></line><line x1="6" y1="20" x2="6" y2="16"></line></svg>',
                'code' => 'reports',
                'name' => 'Reportes',
                'route' => null,
                'active' => request()->routeIs('reports.*'),
                'urls' => $links_reports
            ]);
        endif;

        // //PUNTO DE VENTA
        // if(RoleTrait::hasPermission(51) || RoleTrait::hasPermission(91) || RoleTrait::hasPermission(52) || RoleTrait::hasPermission(54) ):
        //     if(RoleTrait::hasPermission(51)):
        //         $links_selling_point[] = [
        //             'name' => 'Ventas',
        //             'route' => route('pos.index'),
        //             'active' => request()->routeIs('pos.index','pos.index.action'),
        //         ];
        //     endif;
        //     if(RoleTrait::hasPermission(91)):
        //         $links_selling_point[] = [
        //             'name' => 'Ventas generales',
        //             'route' => route('pos.generals.index'),
        //             'active' => request()->routeIs('pos.generals.index','pos.generals.action'),
        //         ];
        //     endif;
        //     if(RoleTrait::hasPermission(52)):
        //         $links_selling_point[] = [
        //             'name' => 'Capturar venta',
        //             'route' => route('pos.capture'),
        //             'active' => request()->routeIs('pos.capture'),
        //         ];
        //     endif;
        //     if(RoleTrait::hasPermission(54)):
        //         $links_selling_point[] = [
        //             'name' => 'Vendedores',
        //             'route' => route('pos.vendors'),
        //             'active' => request()->routeIs('pos.vendors'),
        //         ];
        //     endif;
        //     array_push($links,[
        //         'type' => 'multiple',
        //         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
        //         'code' => 'selling_point',
        //         'name' => 'Punto de venta',
        //         'route' => null,
        //         'active' => request()->routeIs('pos.*'),
        //         'urls' => $links_selling_point
        //     ]);
        // endif;

        //OPERACION
        if(RoleTrait::hasPermission(39) || RoleTrait::hasPermission(47) || RoleTrait::hasPermission(10) || RoleTrait::hasPermission(76) || RoleTrait::hasPermission(78) || RoleTrait::hasPermission(79) ):
            if(RoleTrait::hasPermission(39)):
                $links_operations[] = [
                    'name' => 'Confirmaciones',
                    'route' => route('operation.confirmation'),
                    'active' => request()->routeIs('operation.confirmation','operation.confirmation.search'),
                ];
            endif;            
            if(RoleTrait::hasPermission(47)):
                $links_operations[] = [
                    'name' => 'POS venta',
                    'route' => route('operation.spam'),
                    'active' => request()->routeIs('operation.spam','operation.spam.search'),
                ];
            endif;
            if(RoleTrait::hasPermission(10)):
                $links_operations[] = [
                    'name' => 'Reservaciones',
                    'route' => route('operation.reservations'),
                    'active' => request()->routeIs('operation.reservations','operation.reservations.search'),
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
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-layers"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
                'code' => 'operations',
                'name' => 'Gestion',
                'route' => null,
                'active' => request()->routeIs('operation.*'),
                'urls' => $links_operations
            ]);
        endif;        

        //CONFIGURACIONES
        if(RoleTrait::hasPermission(6) || RoleTrait::hasPermission(1) || RoleTrait::hasPermission(73) || RoleTrait::hasPermission(74) || RoleTrait::hasPermission(75) || RoleTrait::hasPermission(28) || RoleTrait::hasPermission(32)):
            if(RoleTrait::hasPermission(6)):
                $links_settings[] = [
                    'name' => 'Roles',
                    'route' => route('roles.index'),
                    'active' => request()->routeIs('roles.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(1)):
                $links_settings[] = [
                    'name' => 'Usuarios',
                    'route' => route('users.index'),
                    'active' => request()->routeIs('users.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(73)):
                $links_settings[] = [
                    'name' => 'Empresas',
                    'route' => route('enterprises.index'),
                    'active' => request()->routeIs('enterprises.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(74)):
                $links_settings[] = [
                    'name' => 'Vehiculos',
                    'route' => route('vehicles.index'),
                    'active' => request()->routeIs('vehicles.*'),
                ];
            endif;
            if(RoleTrait::hasPermission(75)):
                $links_settings[] = [
                    'name' => 'Conductores',
                    'route' => route('drivers.index'),
                    'active' => request()->routeIs('drivers.*'),
                ];
            endif;
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
                'active' => request()->routeIs('users.*','roles.*','enterprises.*','vehicles.*','drivers.*','config.zones','config.zones.getZones','config.ratesDestination','config.ratesZones'),
                'urls' => $links_settings
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