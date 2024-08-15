@extends('layout.app')
@section('title') Admin Dashboard @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/dashboard2.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/sections/dashboard2.min.css') }}" rel="stylesheet">
    <link href="{{ mix('/assets/css/panel/material.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/panel/material.min.css') }}" rel="stylesheet" >
    <style>
        .custom-tooltip{
            padding: 5px 10px;
            display: flex;
            flex-direction: column;
            gap: 4px;            
        }
        .custom-tooltip .apexcharts-tooltip-y-group{
            padding: 0
        }
        .custom-tooltip .apexcharts-tooltip-y-group .apexcharts-tooltip-text-y-label{
            font-weight: 600;
        }
        .custom-tooltip .apexcharts-tooltip-y-group .apexcharts-tooltip-text-y-value{
            font-weight: 400;
        }
    </style>     
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script>
        let dashboard = {
            dataDay: @json(( isset($bookings_day) ? $bookings_day : [] )),
            dataSitesDay: @json($bookings_sites_day),
            dataDestinationsDay: @json($bookings_destinations_day),            
            dataMonth: @json(( isset($bookings_month) ? $bookings_month : [] )),
            dataSitesMonth: @json($bookings_sites_month),
            dataDestinationsMonth: @json($bookings_destinations_month),
            number_format: function(number, decimals, dec_point, thousands_sep) {
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number,
                    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                    s = '',
                    toFixedFix = function (n, prec) {
                        var k = Math.pow(10, prec);
                        return '' + Math.round(n * k) / k;
                    };
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            },
            getTranslation: function(item){
                return (translations[language][item]) ? translations[language][item] : 'Translate not found';
            },            
            actionTable: function(table){
                let buttons = [];
                const _settings = {},
                    _buttons = table.data('button');

                if( _buttons != undefined && _buttons.length > 0 ){
                    _buttons.forEach(_btn => {
                        buttons.push(_btn);
                    });
                }

                _settings.dom = `<'dt--top-section'<'row'<'col-12 col-sm-6 d-flex justify-content-sm-start justify-content-center'l<'dt-action-buttons align-self-center ms-3'B>><'col-12 col-sm-6 d-flex justify-content-sm-end justify-content-center mt-sm-0 mt-3'f>>>
                                <'table-responsive'tr>
                                <'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>`;                        
                _settings.deferRender = true;
                _settings.responsive = true;
                _settings.buttons = buttons;        
                _settings.order = [[ 0, "DESC" ]];
                _settings.lengthMenu = [10, 20, 50];
                _settings.pageLength = 10;                
                _settings.oLanguage = {
                    "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
                    "sInfo": this.getTranslation("table.pagination") + " _PAGE_ " + this.getTranslation("table.of") + " _PAGES_",
                    "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
                    "sSearchPlaceholder": this.getTranslation("table.search") + "...",
                    "sLengthMenu": this.getTranslation("table.results") + " :  _MENU_",
                };

                table.DataTable( _settings );
            },
            seriesBookingsDay: function() {
                let object = {
                    USD: 0,
                    MXN: 0,
                    series: [{
                        data: []
                    }],
                    labels: []
                };
                const data = this.dataDay;
                if( data.hasOwnProperty('bookings_day') ){
                    const bookings_day = Object.entries(data.bookings_day);
                    bookings_day.forEach( ([date, dataDay]) => {
                        let dateOriginal = date.split('-');
                        let dateFormat = dateOriginal[2] + '/' + dateOriginal[1];
                        object.USD  = object.USD + dataDay.USD;
                        object.MXN  = object.MXN + dataDay.MXN;
                        object.series[0].data.push({                        
                            y: dataDay.counter,
                            x: date + 'T00:00:00',
                            details: dataDay
                        });
                        object.labels.push(date + 'T00:00:00');
                    });
                }
                return object;
            },            
            seriesSitesDay: function() {
                let seriesMonth = {
                    USD: 0,
                    MXN: 0,
                    counter: 0,
                    series: [{
                        data: []
                    }],
                    labels: []
                };            
                const response = (this.dataSitesDay);
                const sites = Object.entries(response.data);
                sites.forEach( ([key, data]) => {
                    // console.log(key, data);
                    seriesMonth.USD  = seriesMonth.USD + data.USD;
                    seriesMonth.MXN  = seriesMonth.MXN + data.MXN;
                    seriesMonth.counter  = seriesMonth.counter + data.counter;
                    seriesMonth.series[0].data.push({                        
                        y: data.counter,
                        x: data.name,
                        details: data
                    });
                    seriesMonth.labels.push(data.name);
                });
                return seriesMonth;
            },
            seriesDestinationDay: function() {
                let seriesMonth = {
                    USD: 0,
                    MXN: 0,
                    counter: 0,
                    series: [{
                        data: []
                    }],
                    labels: [],
                    colors: [],
                };            
                const response = (this.dataDestinationsDay);
                const destinations = Object.entries(response.data);
                destinations.forEach( ([key, data]) => {
                    seriesMonth.USD  = seriesMonth.USD + data.USD;
                    seriesMonth.MXN  = seriesMonth.MXN + data.MXN;
                    seriesMonth.counter  = seriesMonth.counter + data.counter;
                    seriesMonth.series[0].data.push({
                        y: data.counter,
                        x: data.name,
                        details: data
                    });
                    seriesMonth.labels.push(data.name);
                    seriesMonth.colors.push(data.color);
                });
                return seriesMonth;
            },


            seriesBookingsMonth: function() {
                let object = {
                    USD: 0,
                    MXN: 0,
                    series: [{
                        data: []
                    }],
                    labels: []
                };
                const data = this.dataMonth;
                if( data.hasOwnProperty('bookings_day') ){
                    const bookings_day = Object.entries(data.bookings_day);
                    bookings_day.forEach( ([date, dataDay]) => {
                        let dateOriginal = date.split('-');
                        let dateFormat = dateOriginal[2] + '/' + dateOriginal[1];
                        object.USD  = object.USD + dataDay.USD;
                        object.MXN  = object.MXN + dataDay.MXN;
                        object.series[0].data.push({                        
                            y: dataDay.counter,
                            x: date + 'T00:00:00',
                            details: dataDay
                        });
                        object.labels.push(date + 'T00:00:00');
                    });
                }
                return object;
            },
            seriesBookingsCurrencyMonth: function() {
                let object = {
                    USD: 0,
                    MXN: 0,
                    series: [
                        {
                            name: 'USD',
                            data: []
                        },
                        {
                            name: 'MXN',
                            data: []
                        }                                
                    ],
                    labels: []
                };

                const data = this.dataMonth;
                if( data.hasOwnProperty('bookings_day') ){
                    const bookings_day = Object.entries(data.bookings_day);
                    bookings_day.forEach( ([date, dataDay]) => {
                        let dateOriginal = date.split('-');
                        let dateFormat = dateOriginal[2] + '/' + dateOriginal[1];
                        object.USD  = object.USD + dataDay.USD;
                        object.MXN  = object.MXN + dataDay.MXN;
                        object.series[0].data.push(dataDay.USD);
                        object.series[1].data.push(dataDay.MXN);                            
                        object.labels.push(date + 'T00:00:00');
                    });
                }
                return object;
            },
            seriesSitesMonth: function() {
                let seriesMonth = {
                    USD: 0,
                    MXN: 0,
                    counter: 0,
                    series: [{
                        data: []
                    }],
                    labels: []
                };            
                const response = (this.dataSitesMonth);
                const sites = Object.entries(response.data);
                sites.forEach( ([key, data]) => {
                    // console.log(key, data);
                    seriesMonth.USD  = seriesMonth.USD + data.USD;
                    seriesMonth.MXN  = seriesMonth.MXN + data.MXN;
                    seriesMonth.counter  = seriesMonth.counter + data.counter;
                    seriesMonth.series[0].data.push({                        
                        y: data.counter,
                        x: data.name,
                        details: data
                    });
                    seriesMonth.labels.push(data.name);
                });
                return seriesMonth;
            },
            seriesDestinationMonth: function() {
                let seriesMonth = {
                    USD: 0,
                    MXN: 0,
                    counter: 0,
                    series: [{
                        data: []
                    }],
                    labels: [],
                    colors: [],
                };            
                const response = (this.dataDestinationsMonth);
                const destinations = Object.entries(response.data);
                destinations.forEach( ([key, data]) => {
                    seriesMonth.USD  = seriesMonth.USD + data.USD;
                    seriesMonth.MXN  = seriesMonth.MXN + data.MXN;
                    seriesMonth.counter  = seriesMonth.counter + data.counter;
                    seriesMonth.series[0].data.push({
                        y: data.counter,
                        x: data.name,
                        details: data
                    });
                    seriesMonth.labels.push(data.name);
                    seriesMonth.colors.push(data.color);
                });
                return seriesMonth;
            },
        }
    </script>
    <script src="{{ mix('/assets/js/sections/dashboard2.min.js') }}"></script>
@endpush

@section('content')
    <div class="row layout-top-spacing">

        <div class="col-xxl-12 col-sm-12">
            <select name="status" id="status" class="form-control w-25 mb-3">
                <option value="day" selected>Día</option>
                <option value="month">Mes</option>
            </select>
        </div>
        @foreach ($bookings_day['status'] as $key => $status)
            <div class="col-xxl-4 col-sm-6">
                <div class="card_status <?=$key?>">
                    <div class="float-end">
                        <div class="widget-icon day_status">{{ $status['counter'] }}</div>
                        <div class="widget-icon month_status d-none">{{ $bookings_month['status'][$key]['counter'] }}</div>
                    </div>
                    <h6 class="text-uppercase mt-0" title="Customers">{{ strtolower($status['title']) }}</h6>
                    <h2 class="my-2 day_status">$ {{ number_format($status['USD'], 2) }} <span>USD</span></h2>
                    <h2 class="my-2 day_status">$ {{ number_format($status['MXN'], 2) }} <span>MXN</span></h2>

                    <h2 class="my-2 month_status d-none">$ {{ number_format($bookings_month['status'][$key]['USD'], 2) }} <span>USD</span></h2>
                    <h2 class="my-2 month_status d-none">$ {{ number_format($bookings_month['status'][$key]['MXN'], 2) }} <span>MXN</span></h2>
                    <p class="mb-0">
                        <span class="badge bg-white bg-opacity-10 me-1 day_status">{{ number_format(( $status['percentage'] ),2) }}%</span>
                        <span class="badge bg-white bg-opacity-10 me-1 month_status d-none">{{ number_format(( $bookings_month['status'][$key]['percentage'] ),2) }}%</span>
                        <span class="text-nowrap">porcentaje de venta</span>
                    </p>                    
                </div>
            </div>
        @endforeach

        <div class="col-xxl-12 col-sm-12">
            <div class="card_sales">
                <div class="body">
                    <h4 class="header-title mb-4 float-sm-start">Resumen de ventas</h4>
                    <div class="float-sm-end">
                        <ul class="nav nav-pills">
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Día</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Mes</a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row align-items-start">
                        <div class="col-xl-9">
                            <div>
                                <div id="stacked-column-chart" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div>
                        <div class="col-xl-3">
                            <div class="dash-info-widget mt-4 mt-lg-0 py-4 px-3 rounded">
                                <div class="media dash-main-border pb-2 mt-2">
                                   <div class="avatar-sm mb-3 mt-2">
                                      <span class="avatar-title rounded-circle bg-white shadow">
                                        <i class="mdi mdi-credit-card-outline text-primary font-size-18"></i>
                                      </span>
                                   </div>
                                   <div class="media-body ps-3">
                                      <h4 class="font-size-20">$2354</h4>
                                      <p class="text-muted"> <a href="#" class="text-primary">USD <i class="mdi mdi-arrow-right"></i></a>
                                      </p>
                                   </div>
                                </div>
                                <div class="media mt-4">
                                   <div class="avatar-sm mb-3 mt-2">
                                      <span class="avatar-title rounded-circle bg-white shadow">
                                        <i class="mdi mdi-credit-card-outline text-primary font-size-18"></i>
                                      </span>
                                   </div>
                                   <div class="media-body ps-3">
                                      <h4 class="font-size-20">$1598</h4>
                                      <p class="text-muted"> <a href="#" class="text-primary">MXN <i class="mdi mdi-arrow-right"></i></a></p>
                                   </div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-12 col-sm-12">
            <div class="card_bookings">
                <div class="p-3">
                    <h5 class="header-title mb-0">Reservaciones</h5>
                </div>
                <div class="w-100">
                    <div class="table-responsive mt-container-ra w-100 bookings_day">
                        <table class="table table-nowrap table-hover mb-0 w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Monto</th>
                                    <th>Estatus</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- <div class="mt-container-ra w-100 bookings_day" > --}}
                                @if ( isset($bookings_month['bookings']) )
                                    @foreach ($bookings_month['bookings'] as $key => $booking)
                                        <tr>
                                            <td><a href="{{ route('reservations.details',['id' => $booking->id]) }}">{{ $booking->reservation_codes }}</a></td>
                                            <td>{{ $booking->client_full_name }}</td>
                                            <td>$ {{ number_format($booking->total_sales,2) }} {{ $booking->currency }}</td>
                                            <td><span class="badge bg-{{ strtolower($booking->status) }}-subtle text-{{ strtolower($booking->status) }}">{{ strtolower($booking->status) }}</span></td>
                                        </tr>                                    
                                    @endforeach
                                @endif
                                {{-- </div> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div id="filters" class="accordion">
                <div class="card">
                <div class="card-header" id="headingOne1">
                    <section class="mb-0 mt-0">
                        <div role="menu" class="" data-bs-toggle="collapse" data-bs-target="#defaultAccordionOne" aria-expanded="true" aria-controls="defaultAccordionOne">
                            Filtro de reportes <div class="icons"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg></div>
                        </div>
                    </section>
                </div>
                <div id="defaultAccordionOne" class="collapse show" aria-labelledby="headingOne1" data-bs-parent="#filters">
                    <div class="card-body">
                        <form action="" class="row" id="formFilter">
                            <div class="col-12 col-sm-5 mb-3 mb-lg-0">
                                <label class="form-label" for="lookup_date">Fecha de creación</label>
                                <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $data['init'] }} - {{ $data['end'] }}">
                            </div>
                            <div class="col-12 col-sm-3 align-self-end">
                                <button type="submit" class="btn btn-primary btn-lg btn-filter w-100">Filtrar</button>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
            </div>
        </div> --}}

        <div class="row mb-3 d-none">
            <div class="col-md-12">
                <ul class="nav nav-pills" id="animateLine" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" data-action="day" id="animated-underline-day-tab" data-bs-toggle="tab" href="#animated-underline-day" role="tab" aria-controls="animated-underline-day" aria-selected="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-credit-card"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg> 
                            Reservas de día
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-action="month" id="animated-underline-month-tab" data-bs-toggle="tab" href="#animated-underline-month" role="tab" aria-controls="animated-underline-month" aria-selected="false" tabindex="-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-dollar-sign"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> 
                            Reservas del mes
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        @php
            // dump($bookings_month['status']);
        @endphp
        <div class="tab-content d-none" id="animateLineContent-4">
            <div class="tab-pane fade show active" id="animated-underline-day" role="tabpanel" aria-labelledby="animated-underline-day-tab">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                        <div class="section general-info">
                            <div class="row info">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-three">
                                        <div class="widget-heading">
                                            <h5 class="">Historial de reservas por sitio del día</h5>
                                        </div>        
                                        <div class="widget-content">
                                            <div id="bookingsAnalyticsSitesDay"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-three">
                                        <div class="widget-heading">
                                            <h5 class="">Historial de reservas por destino del día</h5>
                                        </div>        
                                        <div class="widget-content">
                                            <div id="bookingsAnalyticsDestinationsDay"></div>
                                        </div>
                                    </div>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="animated-underline-month" role="tabpanel" aria-labelledby="animated-underline-month-tab">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                        <div class="section general-info">
                            <div class="row info">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-one">
                                        <div class="widget-heading">
                                            <h5 class="">Historial de reservas y ganancias del més</h5>
                                        </div>
                                        <div class="widget-content">
                                            <div id="bookingsAnalyticsMonth"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-three">
                                        <div class="widget-heading">
                                            <h5 class="">Historial de reservas por divisas del més</h5>
                                        </div>        
                                        <div class="widget-content">
                                            <div id="bookingsAnalyticsCurrencyMonth"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-three">
                                        <div class="widget-heading">
                                            <h5 class="">Historial de reservas por sitio del més</h5>
                                        </div>        
                                        <div class="widget-content">
                                            <div id="bookingsAnalyticsSitesMonth"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-three">
                                        <div class="widget-heading">
                                            <h5 class="">Historial de reservas por destino del més</h5>
                                        </div>        
                                        <div class="widget-content">
                                            <div id="bookingsAnalyticsDestinationsMonth"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>                                   
                </div>
            </div>
        </div>
    </div>

    <x-modals.reservations.bookings_day :bookings="$bookings_day" />
    <x-modals.reservations.bookings_month :bookings="$bookings_month" />
@endsection