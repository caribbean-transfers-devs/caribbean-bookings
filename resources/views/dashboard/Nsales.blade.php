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
            <div class="card mb-3">
                <div class="card-body">
                    <form action="" class="row" id="formFilter">
                        <div class="col-12 col-sm-5 mb-3 mb-lg-0">
                            <label class="form-label" for="lookup_date">Fecha de creación</label>
                            <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $data['init'] }} - {{ $data['end'] }}">
                        </div>
                        <div class="col-12 col-sm-3 align-self-end">
                            <button type="submit" class="btn btn-primary btn-lg btn-filter w-100">Filtrar</button>
                        </div>
                        <div class="col-12 col-sm-3 mb-3 mb-lg-0">
                            <label class="form-label" for="">Selecciona una opción</label>
                            <select name="status" id="status" class="form-control">
                                @if ( !$flag_month )
                                    <option value="day" selected>Día</option>    
                                @endif                                
                                <option value="month" <?=( $flag_month ? 'selected' : '' )?>>Mes</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
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
                        <ul class="nav nav-pills" id="items">
                            <li class="nav-item">
                                <a class="nav-link active" id="item_day" href="javascript:void(0);">Día</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="item_month" href="javascript:void(0);">Mes</a>
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
                                      <h4 class="font-size-20" id="total_sales_usd"></h4>
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
                                      <h4 class="font-size-20" id="total_sales_mxn"></h4>
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
            <div class="card_sales">
                <div class="body">
                    <h4 class="header-title mb-4 float-sm-start">Historial</h4>
                    <div class="float-sm-end">
                        <ul class="nav nav-pills align-items-center" id="itemsOptions">
                            <li class="me-2">
                                <select name="option" id="option" class="form-control">
                                    <option value="sites" selected>Sitios</option>
                                    <option value="destinations">Destinos</option>
                                </select>                                
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" id="itemOption_day" href="javascript:void(0);">Día</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="itemOption_month" href="javascript:void(0);">Mes</a>
                            </li>
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                    <div class="row align-items-start">
                        <div class="col-xl-12">
                            <div>
                                <div id="stacked-column-chart2" class="apex-charts" dir="ltr"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection