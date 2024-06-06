    @php
        use App\Traits\RoleTrait;
    @endphp
    @php
        $links = [
            // [
            //     'type' => 'single',
            //     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
            //     'code' => 'dashboard',
            //     'name' => __('sidebar.Dashboard'),
            //     'route' => route('dashboard.affiliates',["type" => "affiliate"]),
            //     'active' => request()->routeIs('dashboard')
            // ],

            // [
            //     'type' => 'multiple',
            //     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
            //     'code' => 'dashboard',
            //     'name' => __('sidebar.Dashboard'),
            //     'route' => null,
            //     'active' => request()->routeIs('dashboard.*'),
            //     'urls' => [
            //         array(
            //             'name' => __('sidebar.affiliate'),
            //             'route' => route('dashboard.affiliates',["type" => "affiliate"]),
            //             'active' => request()->is('dashboard/affiliate'),
            //         ),
            //         array(
            //             'name' => __('sidebar.subaffiliates'),
            //             'route' => route('dashboard.affiliates', ["type" => "subaffiliate"]),
            //             'active' => request()->is('dashboard/subaffiliate'),
            //         ),
            //     ]
            // ],

            // [
            //     'type' => 'single',
            //     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>',
            //     'code' => 'finances',
            //     'name' => __('sidebar.Finance'),
            //     'route' => route('finances'),
            //     'active' => request()->routeIs('finances')
            // ], 

            // [
            //     'type' => 'single',
            //     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clipboard"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>',
            //     'code' => 'tpv',
            //     'name' => __('sidebar.TPV'),
            //     'route' => ( app()->getLocale() == "es" ? route('tpv.book.es', ['locale' => app()->getLocale(), 'id' => Session('token_data')['id']]) : route('tpv.book', ['id' => Session('token_data')['id']]) ),
            //     'active' => request()->routeIs('tpv.book')
            // ],
            // [
            //     'type' => 'single',
            //     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M0 80C0 53.5 21.5 32 48 32h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V80zM64 96v64h64V96H64zM0 336c0-26.5 21.5-48 48-48h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V336zm64 16v64h64V352H64zM304 32h96c26.5 0 48 21.5 48 48v96c0 26.5-21.5 48-48 48H304c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48zm80 64H320v64h64V96zM256 304c0-8.8 7.2-16 16-16h64c8.8 0 16 7.2 16 16s7.2 16 16 16h32c8.8 0 16-7.2 16-16s7.2-16 16-16s16 7.2 16 16v96c0 8.8-7.2 16-16 16H368c-8.8 0-16-7.2-16-16s-7.2-16-16-16s-16 7.2-16 16v64c0 8.8-7.2 16-16 16H272c-8.8 0-16-7.2-16-16V304zM368 480a16 16 0 1 1 0-32 16 16 0 1 1 0 32zm64 0a16 16 0 1 1 0-32 16 16 0 1 1 0 32z" fill="#030305"/></svg>',
            //     'code' => 'tpv',
            //     'name' => __('sidebar.Scanme'),
            //     'route' => route('tpv.qr', ['id' => Session('token_data')['id']]),
            //     'active' => request()->routeIs('tpv.qr')
            // ],
            // [
            //     'type' => 'single',
            //     'icon' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path></svg>',
            //     'code' => 'tools',
            //     'name' => __('sidebar.Tools'),
            //     'route' => route('tools', ['id' => Session('token_data')['id']]),
            //     'active' => request()->routeIs('tools'),
            // ],
        ];

        $links_dashboard = array();
        $links_reports = array();
        $links_selling_point = array();
        $links_operations = array();
        $links_settings = array();
        $links_administration = array();

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

        if(RoleTrait::hasPermission(43) || RoleTrait::hasPermission(44) || RoleTrait::hasPermission(45) || RoleTrait::hasPermission(50)):
            if(RoleTrait::hasPermission(43)):
                $links_reports[] = [
                    'name' => 'Pagos',
                    'route' => route('reports.payment'),
                    'active' => request()->routeIs('reports.payment'),
                ];
            endif;
            if(RoleTrait::hasPermission(44)):
                $links_reports[] = [
                    'name' => 'Ventas',
                    'route' => route('reports.sales'),
                    'active' => request()->routeIs('reports.sales'),
                ];
            endif;
            if(RoleTrait::hasPermission(45)):
                $links_reports[] = [
                    'name' => 'Comisiones',
                    'route' => route('reports.commissions'),
                    'active' => request()->routeIs('reports.commissions'),
                ];
            endif;
            if(RoleTrait::hasPermission(50)):
                $links_reports[] = [
                    'name' => 'Efectivo',
                    'route' => route('reports.cash'),
                    'active' => request()->routeIs('reports.cash'),
                ];
            endif;            
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'reports',
                'name' => 'Reportes',
                'route' => null,
                'active' => request()->routeIs('reports.*'),
                'urls' => $links_selling_point
            ]);            
        endif;        

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

        if(RoleTrait::hasPermission(51) || RoleTrait::hasPermission(52) || RoleTrait::hasPermission(54) ):
            if(RoleTrait::hasPermission(51)):
                $links_selling_point[] = [
                    'name' => 'Ventas',
                    'route' => route('pos.index'),
                    'active' => request()->routeIs('pos.index'),
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

        if(RoleTrait::hasPermission(36) || RoleTrait::hasPermission(37) || RoleTrait::hasPermission(39) || RoleTrait::hasPermission(46) || RoleTrait::hasPermission(47) ):
            if(RoleTrait::hasPermission(36)):
                $links_operations[] = [
                    'name' => 'Descargar',
                    'route' => route('operation.index'),
                    'active' => request()->routeIs('operation.index'),
                ];
            endif;
            if(RoleTrait::hasPermission(37)):
                $links_operations[] = [
                    'name' => 'Gestión',
                    'route' => route('operation.managment'),
                    'active' => request()->routeIs('operation.managment'),
                ];
            endif;
            if(RoleTrait::hasPermission(39)):
                $links_operations[] = [
                    'name' => 'Confirmaciones',
                    'route' => route('operation.confirmation'),
                    'active' => request()->routeIs('operation.confirmation'),
                ];
            endif;
            if(RoleTrait::hasPermission(46)):
                $links_operations[] = [
                    'name' => 'CC Form',
                    'route' => route('reports.ccform'),
                    'active' => request()->routeIs('reports.ccform'),
                ];
            endif;
            if(RoleTrait::hasPermission(47)):
                $links_operations[] = [
                    'name' => 'SPAM',
                    'route' => route('operation.spam'),
                    'active' => request()->routeIs('operation.spam'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'operation',
                'name' => 'Operación',
                'route' => null,
                'active' => request()->routeIs('operation.*'.'reports.*'),
                'urls' => $links_operations
            ]);            
        endif;

        if(RoleTrait::hasPermission(10)):
            array_push($links,[
                'type' => 'single',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-inbox"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>',
                'code' => 'bookings',
                'name' => 'Reservaciones',
                'route' => route('reservations.index'),
                'active' => request()->routeIs('reservations.index')
            ]);        
        endif;

        if(RoleTrait::hasPermission(28) || RoleTrait::hasPermission(32)):
            if(RoleTrait::hasPermission(28)):
                $links_settings[] = [
                    'name' => 'Zonas',
                    'route' => route('config.zones'),
                    'active' => request()->routeIs('config.zones'),
                ];
            endif;
            if(RoleTrait::hasPermission(32)):
                $links_settings[] = [
                    'name' => 'Tarifas',
                    'route' => route('config.ratesDestination'),
                    'active' => request()->routeIs('config.ratesDestination'),
                ];
            endif;
            array_push($links,[
                'type' => 'multiple',
                'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-settings"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
                'code' => 'settings',
                'name' => 'Configuraciones',
                'route' => null,
                'active' => request()->routeIs('config.zones','config.ratesDestination'),
                'urls' => $links_settings
            ]);            
        endif;

        if(RoleTrait::hasPermission(1) || RoleTrait::hasPermission(6)):
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
            $links_administration[] = [
                'name' => 'Empresas',
                'route' => route('enterprises.index'),
                'active' => request()->routeIs('enterprises.*'),
            ];
            $links_administration[] = [
                'name' => 'Vehiculos',
                'route' => route('vehicles.index'),
                'active' => request()->routeIs('vehicles.*'),
            ];
            $links_administration[] = [
                'name' => 'Conductores',
                'route' => route('drivers.index'),
                'active' => request()->routeIs('drivers.*'),
            ];            
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
    {{-- <pre>
        @php
            print_r($links);
        @endphp
    </pre> --}}
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