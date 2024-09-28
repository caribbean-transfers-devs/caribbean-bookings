@extends('layout.dashboard')
@section('title') Admin Dashboard @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/dashboard.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/sections/dashboard.min.css') }}" rel="stylesheet">
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
            seriesStatusDay: function() {
                let object = {
                    USD: 0,
                    MXN: 0,                            
                    series: [],
                    labels: [],
                    colors: []
                };
                const data = this.dataDay;
                if( data.hasOwnProperty('status') ){
                    const status = Object.entries(data.status);                
                    status.forEach( ([key, data]) => {
                        object.series.push(data.counter);
                        object.labels.push(key);
                        object.colors.push(data.color);
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
            seriesStatusMonth: function() {
                let object = {
                    USD: 0,
                    MXN: 0,                            
                    series: [],
                    labels: [],
                    colors: []
                };
                const data = this.dataMonth;
                if( data.hasOwnProperty('status') ){
                    const status = Object.entries(data.status);                
                    status.forEach( ([key, data]) => {
                        object.series.push(data.counter);
                        object.labels.push(key);
                        object.colors.push(data.color);
                    });
                }
                return object;
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
    <script src="{{ mix('/assets/js/sections/dashboard.min.js') }}"></script>
@endpush

@section('content')

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
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
        </div>

        <div class="row mb-3">
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

        <div class="tab-content" id="animateLineContent-4">
            <div class="tab-pane fade show active" id="animated-underline-day" role="tabpanel" aria-labelledby="animated-underline-day-tab">
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                        <div class="section general-info">
                            <div class="row info">
                                @if ( isset($bookings_day['status']) )
                                    @foreach ($bookings_day['status'] as $key => $statu)
                                        @php
                                            // dump($statu);
                                        @endphp
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                            <div class="widget widget-six">
                                                <div class="widget-heading">
                                                    <h6 class="">{{ strtolower($statu['title']) }}</h6>
                                                </div>
                                                <div class="w-chart">
                                                    <div class="w-chart-section">
                                                        <div class="w-detail">
                                                            <p class="w-title">Total USD</p>
                                                            <p class="w-stats">$ {{ number_format($statu['USD'], 2) }}</p>
                                                        </div>
                                                        <div class="w-chart-render-one">
                                                            <div id="total-users" style="min-height: 80px;"><div id="apexchartsuniquexvisits" class="apexcharts-canvas apexchartsuniquexvisits apexcharts-theme-light" style="width: 165px; height: 80px;"><svg id="SvgjsSvg5106" width="165" height="80" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG5108" class="apexcharts-inner apexcharts-graphical" transform="translate(40, 35)"><defs id="SvgjsDefs5107"><clipPath id="gridRectMaskfbhinuds"><rect id="SvgjsRect5114" width="131" height="47" x="-3" y="-1" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMaskfbhinuds"></clipPath><clipPath id="nonForecastMaskfbhinuds"></clipPath><clipPath id="gridRectMarkerMaskfbhinuds"><rect id="SvgjsRect5115" width="129" height="49" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><filter id="SvgjsFilter5121" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feFlood id="SvgjsFeFlood5122" flood-color="#e2a03f" flood-opacity="0.7" result="SvgjsFeFlood5122Out" in="SourceGraphic"></feFlood><feComposite id="SvgjsFeComposite5123" in="SvgjsFeFlood5122Out" in2="SourceAlpha" operator="in" result="SvgjsFeComposite5123Out"></feComposite><feOffset id="SvgjsFeOffset5124" dx="1" dy="1" result="SvgjsFeOffset5124Out" in="SvgjsFeComposite5123Out"></feOffset><feGaussianBlur id="SvgjsFeGaussianBlur5125" stdDeviation="2 " result="SvgjsFeGaussianBlur5125Out" in="SvgjsFeOffset5124Out"></feGaussianBlur><feMerge id="SvgjsFeMerge5126" result="SvgjsFeMerge5126Out" in="SourceGraphic"><feMergeNode id="SvgjsFeMergeNode5127" in="SvgjsFeGaussianBlur5125Out"></feMergeNode><feMergeNode id="SvgjsFeMergeNode5128" in="[object Arguments]"></feMergeNode></feMerge><feBlend id="SvgjsFeBlend5129" in="SourceGraphic" in2="SvgjsFeMerge5126Out" mode="normal" result="SvgjsFeBlend5129Out"></feBlend></filter></defs><line id="SvgjsLine5113" x1="0" y1="0" x2="0" y2="45" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="45" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG5130" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG5131" class="apexcharts-xaxis-texts-g" transform="translate(0, 4)"></g></g><g id="SvgjsG5143" class="apexcharts-grid"><g id="SvgjsG5144" class="apexcharts-gridlines-horizontal" style="display: none;"><line id="SvgjsLine5146" x1="0" y1="0" x2="125" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5147" x1="0" y1="6.428571428571429" x2="125" y2="6.428571428571429" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5148" x1="0" y1="12.857142857142858" x2="125" y2="12.857142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5149" x1="0" y1="19.285714285714285" x2="125" y2="19.285714285714285" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5150" x1="0" y1="25.714285714285715" x2="125" y2="25.714285714285715" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5151" x1="0" y1="32.142857142857146" x2="125" y2="32.142857142857146" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5152" x1="0" y1="38.57142857142858" x2="125" y2="38.57142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5153" x1="0" y1="45.00000000000001" x2="125" y2="45.00000000000001" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line></g><g id="SvgjsG5145" class="apexcharts-gridlines-vertical" style="display: none;"></g><line id="SvgjsLine5155" x1="0" y1="45" x2="125" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line><line id="SvgjsLine5154" x1="0" y1="1" x2="0" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line></g><g id="SvgjsG5116" class="apexcharts-line-series apexcharts-plot-series"><g id="SvgjsG5117" class="apexcharts-series" seriesName="seriesx1" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath5120" d="M 0 31.5C 4.861111111111111 31.5 9.027777777777779 39.214285714285715 13.88888888888889 39.214285714285715C 18.75 39.214285714285715 22.916666666666668 21.857142857142858 27.77777777777778 21.857142857142858C 32.63888888888889 21.857142857142858 36.80555555555556 37.285714285714285 41.66666666666667 37.285714285714285C 46.52777777777778 37.285714285714285 50.69444444444445 16.714285714285715 55.55555555555556 16.714285714285715C 60.416666666666664 16.714285714285715 64.58333333333333 28.92857142857143 69.44444444444444 28.92857142857143C 74.30555555555556 28.92857142857143 78.47222222222223 7.071428571428569 83.33333333333334 7.071428571428569C 88.19444444444446 7.071428571428569 92.36111111111111 18.642857142857142 97.22222222222223 18.642857142857142C 102.08333333333334 18.642857142857142 106.25 2.5714285714285694 111.11111111111111 2.5714285714285694C 115.97222222222223 2.5714285714285694 120.1388888888889 28.92857142857143 125.00000000000001 28.92857142857143" fill="none" fill-opacity="1" stroke="rgba(226,160,63,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMaskfbhinuds)" filter="url(#SvgjsFilter5121)" pathTo="M 0 31.5C 4.861111111111111 31.5 9.027777777777779 39.214285714285715 13.88888888888889 39.214285714285715C 18.75 39.214285714285715 22.916666666666668 21.857142857142858 27.77777777777778 21.857142857142858C 32.63888888888889 21.857142857142858 36.80555555555556 37.285714285714285 41.66666666666667 37.285714285714285C 46.52777777777778 37.285714285714285 50.69444444444445 16.714285714285715 55.55555555555556 16.714285714285715C 60.416666666666664 16.714285714285715 64.58333333333333 28.92857142857143 69.44444444444444 28.92857142857143C 74.30555555555556 28.92857142857143 78.47222222222223 7.071428571428569 83.33333333333334 7.071428571428569C 88.19444444444446 7.071428571428569 92.36111111111111 18.642857142857142 97.22222222222223 18.642857142857142C 102.08333333333334 18.642857142857142 106.25 2.5714285714285694 111.11111111111111 2.5714285714285694C 115.97222222222223 2.5714285714285694 120.1388888888889 28.92857142857143 125.00000000000001 28.92857142857143" pathFrom="M -1 45L -1 45L 13.88888888888889 45L 27.77777777777778 45L 41.66666666666667 45L 55.55555555555556 45L 69.44444444444444 45L 83.33333333333334 45L 97.22222222222223 45L 111.11111111111111 45L 125.00000000000001 45"></path><g id="SvgjsG5118" class="apexcharts-series-markers-wrap" data:realIndex="0"><g class="apexcharts-series-markers"><circle id="SvgjsCircle5161" r="0" cx="0" cy="0" class="apexcharts-marker wujm5tsvz no-pointer-events" stroke="#ffffff" fill="#e2a03f" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g></g><g id="SvgjsG5119" class="apexcharts-datalabels" data:realIndex="0"></g></g><line id="SvgjsLine5156" x1="0" y1="0" x2="125" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine5157" x1="0" y1="0" x2="125" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG5158" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG5159" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG5160" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect5112" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect><g id="SvgjsG5142" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g><g id="SvgjsG5109" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend" style="max-height: 40px;"></div><div class="apexcharts-tooltip apexcharts-theme-dark"><div class="apexcharts-tooltip-series-group" style="order: 1;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(226, 160, 63);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-dark"><div class="apexcharts-yaxistooltip-text"></div></div></div></div>
                                                        </div>                                
                                                    </div>    
                                                    <div class="w-chart-section">
                                                        <div class="w-detail">
                                                            <p class="w-title">Total MXN</p>
                                                            <p class="w-stats">$ {{ number_format($statu['MXN'], 2) }}</p>
                                                        </div>
                                                        <div class="w-chart-render-one">
                                                            <div id="paid-visits" style="min-height: 80px;"><div id="apexchartstotalxusers" class="apexcharts-canvas apexchartstotalxusers apexcharts-theme-light" style="width: 164px; height: 80px;"><svg id="SvgjsSvg5049" width="164" height="80" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG5051" class="apexcharts-inner apexcharts-graphical" transform="translate(40, 35)"><defs id="SvgjsDefs5050"><clipPath id="gridRectMasko3hakam7"><rect id="SvgjsRect5057" width="130" height="47" x="-3" y="-1" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMasko3hakam7"></clipPath><clipPath id="nonForecastMasko3hakam7"></clipPath><clipPath id="gridRectMarkerMasko3hakam7"><rect id="SvgjsRect5058" width="128" height="49" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><filter id="SvgjsFilter5064" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feFlood id="SvgjsFeFlood5065" flood-color="#009688" flood-opacity="0.7" result="SvgjsFeFlood5065Out" in="SourceGraphic"></feFlood><feComposite id="SvgjsFeComposite5066" in="SvgjsFeFlood5065Out" in2="SourceAlpha" operator="in" result="SvgjsFeComposite5066Out"></feComposite><feOffset id="SvgjsFeOffset5067" dx="1" dy="3" result="SvgjsFeOffset5067Out" in="SvgjsFeComposite5066Out"></feOffset><feGaussianBlur id="SvgjsFeGaussianBlur5068" stdDeviation="3 " result="SvgjsFeGaussianBlur5068Out" in="SvgjsFeOffset5067Out"></feGaussianBlur><feMerge id="SvgjsFeMerge5069" result="SvgjsFeMerge5069Out" in="SourceGraphic"><feMergeNode id="SvgjsFeMergeNode5070" in="SvgjsFeGaussianBlur5068Out"></feMergeNode><feMergeNode id="SvgjsFeMergeNode5071" in="[object Arguments]"></feMergeNode></feMerge><feBlend id="SvgjsFeBlend5072" in="SourceGraphic" in2="SvgjsFeMerge5069Out" mode="normal" result="SvgjsFeBlend5072Out"></feBlend></filter></defs><line id="SvgjsLine5056" x1="0" y1="0" x2="0" y2="45" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="45" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG5073" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG5074" class="apexcharts-xaxis-texts-g" transform="translate(0, 4)"></g></g><g id="SvgjsG5086" class="apexcharts-grid"><g id="SvgjsG5087" class="apexcharts-gridlines-horizontal" style="display: none;"><line id="SvgjsLine5089" x1="0" y1="0" x2="124" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5090" x1="0" y1="6.428571428571429" x2="124" y2="6.428571428571429" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5091" x1="0" y1="12.857142857142858" x2="124" y2="12.857142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5092" x1="0" y1="19.285714285714285" x2="124" y2="19.285714285714285" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5093" x1="0" y1="25.714285714285715" x2="124" y2="25.714285714285715" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5094" x1="0" y1="32.142857142857146" x2="124" y2="32.142857142857146" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5095" x1="0" y1="38.57142857142858" x2="124" y2="38.57142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5096" x1="0" y1="45.00000000000001" x2="124" y2="45.00000000000001" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line></g><g id="SvgjsG5088" class="apexcharts-gridlines-vertical" style="display: none;"></g><line id="SvgjsLine5098" x1="0" y1="45" x2="124" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line><line id="SvgjsLine5097" x1="0" y1="1" x2="0" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line></g><g id="SvgjsG5059" class="apexcharts-line-series apexcharts-plot-series"><g id="SvgjsG5060" class="apexcharts-series" seriesName="seriesx1" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath5063" d="M 0 37.28571428571429C 4.822222222222222 37.28571428571429 8.955555555555556 39.214285714285715 13.777777777777777 39.214285714285715C 18.599999999999998 39.214285714285715 22.73333333333333 32.142857142857146 27.555555555555554 32.142857142857146C 32.37777777777777 32.142857142857146 36.511111111111106 21.214285714285715 41.33333333333333 21.214285714285715C 46.15555555555555 21.214285714285715 50.288888888888884 30.85714285714286 55.11111111111111 30.85714285714286C 59.93333333333333 30.85714285714286 64.06666666666666 23.142857142857146 68.88888888888889 23.142857142857146C 73.71111111111111 23.142857142857146 77.84444444444443 29.571428571428573 82.66666666666666 29.571428571428573C 87.48888888888888 29.571428571428573 91.62222222222222 16.071428571428577 96.44444444444444 16.071428571428577C 101.26666666666667 16.071428571428577 105.39999999999999 25.071428571428573 110.22222222222221 25.071428571428573C 115.04444444444444 25.071428571428573 119.17777777777776 7.0714285714285765 123.99999999999999 7.0714285714285765" fill="none" fill-opacity="1" stroke="rgba(0,150,136,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMasko3hakam7)" filter="url(#SvgjsFilter5064)" pathTo="M 0 37.28571428571429C 4.822222222222222 37.28571428571429 8.955555555555556 39.214285714285715 13.777777777777777 39.214285714285715C 18.599999999999998 39.214285714285715 22.73333333333333 32.142857142857146 27.555555555555554 32.142857142857146C 32.37777777777777 32.142857142857146 36.511111111111106 21.214285714285715 41.33333333333333 21.214285714285715C 46.15555555555555 21.214285714285715 50.288888888888884 30.85714285714286 55.11111111111111 30.85714285714286C 59.93333333333333 30.85714285714286 64.06666666666666 23.142857142857146 68.88888888888889 23.142857142857146C 73.71111111111111 23.142857142857146 77.84444444444443 29.571428571428573 82.66666666666666 29.571428571428573C 87.48888888888888 29.571428571428573 91.62222222222222 16.071428571428577 96.44444444444444 16.071428571428577C 101.26666666666667 16.071428571428577 105.39999999999999 25.071428571428573 110.22222222222221 25.071428571428573C 115.04444444444444 25.071428571428573 119.17777777777776 7.0714285714285765 123.99999999999999 7.0714285714285765" pathFrom="M -1 51.42857142857143L -1 51.42857142857143L 13.777777777777777 51.42857142857143L 27.555555555555554 51.42857142857143L 41.33333333333333 51.42857142857143L 55.11111111111111 51.42857142857143L 68.88888888888889 51.42857142857143L 82.66666666666666 51.42857142857143L 96.44444444444444 51.42857142857143L 110.22222222222221 51.42857142857143L 123.99999999999999 51.42857142857143"></path><g id="SvgjsG5061" class="apexcharts-series-markers-wrap" data:realIndex="0"><g class="apexcharts-series-markers"><circle id="SvgjsCircle5104" r="0" cx="0" cy="0" class="apexcharts-marker wzs7ugkk2 no-pointer-events" stroke="#ffffff" fill="#009688" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g></g><g id="SvgjsG5062" class="apexcharts-datalabels" data:realIndex="0"></g></g><line id="SvgjsLine5099" x1="0" y1="0" x2="124" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine5100" x1="0" y1="0" x2="124" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG5101" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG5102" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG5103" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect5055" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect><g id="SvgjsG5085" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g><g id="SvgjsG5052" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend" style="max-height: 40px;"></div><div class="apexcharts-tooltip apexcharts-theme-dark"><div class="apexcharts-tooltip-series-group" style="order: 1;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(0, 150, 136);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-dark"><div class="apexcharts-yaxistooltip-text"></div></div></div></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>            
                                        </div>
                                    @endforeach
                                @endif

                                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-activity-four">
                                        <div class="widget-heading">
                                            <h5 class="">Reservas del día</h5>
                                        </div>
                                        <div class="widget-content">
                                            <div class="mt-container-ra mx-auto bookings_day">
                                                <div class="timeline-line">
                                                    @if ( isset($bookings_day['bookings']) )
                                                        @for ($i = 0; $i < 15; $i++)
                                                            @php
                                                                // dump($booking);
                                                                $booking = ( isset($bookings_day['bookings'][$i]) ) ? $bookings_day['bookings'][$i] : [] ;
                                                            @endphp
                                                            @if ( !empty($booking) )
                                                                <div class="item-timeline timeline-<?= ( $booking->status == "CONFIRMED" ? "success" : ( $booking->status == "PENDING" ? "warning" : "danger" ) ) ?>">
                                                                    <div class="t-dot" data-original-title="" title="">
                                                                    </div>
                                                                    <div class="t-text">
                                                                        <p><span>{{ $booking->full_name }}</span> <a href="{{ route('reservations.details',['id' => $booking->id]) }}">{{ $booking->reservation_codes }}</a></p>
                                                                        <span class="badge">{{ strtolower($booking->status) }}</span>
                                                                        <p class="t-time">$ {{ number_format($booking->total_sales,2) }} {{ $booking->currency }}</p>
                                                                    </div>
                                                                </div>                                                            
                                                            @endif
                                                        @endfor
                                                    @endif
                                                </div>
                                            </div>
                                            @if ( isset($bookings_day['bookings']) && count($bookings_day['bookings']) > 15 )
                                                {{-- <div class="tm-action-btn">
                                                    <button class="btn" data-bs-toggle="modal" data-bs-target="#bookingsDayModal"><span>Ver todo</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></button>
                                                </div> --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-two">
                                        <div class="widget-heading">
                                            <h5 class="">Reservas por estatus</h5>
                                        </div>
                                        <div class="widget-content">
                                            <div id="bookingsStatusDay" class=""></div>
                                        </div>
                                    </div>
                                </div>

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
                                @if ( isset($bookings_month['status']) )
                                    @foreach ($bookings_month['status'] as $key3 => $statu)
                                        @php
                                            // dump($statu);
                                        @endphp
                                        <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                            <div class="widget widget-six">
                                                <div class="widget-heading">
                                                    <h6 class="">{{ strtolower($statu['title']) }}</h6>
                                                </div>
                                                <div class="w-chart">
                                                    <div class="w-chart-section">
                                                        <div class="w-detail">
                                                            <p class="w-title">Total USD</p>
                                                            <p class="w-stats">$ {{ number_format($statu['USD'], 2) }}</p>
                                                        </div>
                                                        <div class="w-chart-render-one">
                                                            <div id="total-users" style="min-height: 80px;"><div id="apexchartsuniquexvisits" class="apexcharts-canvas apexchartsuniquexvisits apexcharts-theme-light" style="width: 165px; height: 80px;"><svg id="SvgjsSvg5106" width="165" height="80" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG5108" class="apexcharts-inner apexcharts-graphical" transform="translate(40, 35)"><defs id="SvgjsDefs5107"><clipPath id="gridRectMaskfbhinuds"><rect id="SvgjsRect5114" width="131" height="47" x="-3" y="-1" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMaskfbhinuds"></clipPath><clipPath id="nonForecastMaskfbhinuds"></clipPath><clipPath id="gridRectMarkerMaskfbhinuds"><rect id="SvgjsRect5115" width="129" height="49" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><filter id="SvgjsFilter5121" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feFlood id="SvgjsFeFlood5122" flood-color="#e2a03f" flood-opacity="0.7" result="SvgjsFeFlood5122Out" in="SourceGraphic"></feFlood><feComposite id="SvgjsFeComposite5123" in="SvgjsFeFlood5122Out" in2="SourceAlpha" operator="in" result="SvgjsFeComposite5123Out"></feComposite><feOffset id="SvgjsFeOffset5124" dx="1" dy="1" result="SvgjsFeOffset5124Out" in="SvgjsFeComposite5123Out"></feOffset><feGaussianBlur id="SvgjsFeGaussianBlur5125" stdDeviation="2 " result="SvgjsFeGaussianBlur5125Out" in="SvgjsFeOffset5124Out"></feGaussianBlur><feMerge id="SvgjsFeMerge5126" result="SvgjsFeMerge5126Out" in="SourceGraphic"><feMergeNode id="SvgjsFeMergeNode5127" in="SvgjsFeGaussianBlur5125Out"></feMergeNode><feMergeNode id="SvgjsFeMergeNode5128" in="[object Arguments]"></feMergeNode></feMerge><feBlend id="SvgjsFeBlend5129" in="SourceGraphic" in2="SvgjsFeMerge5126Out" mode="normal" result="SvgjsFeBlend5129Out"></feBlend></filter></defs><line id="SvgjsLine5113" x1="0" y1="0" x2="0" y2="45" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="45" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG5130" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG5131" class="apexcharts-xaxis-texts-g" transform="translate(0, 4)"></g></g><g id="SvgjsG5143" class="apexcharts-grid"><g id="SvgjsG5144" class="apexcharts-gridlines-horizontal" style="display: none;"><line id="SvgjsLine5146" x1="0" y1="0" x2="125" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5147" x1="0" y1="6.428571428571429" x2="125" y2="6.428571428571429" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5148" x1="0" y1="12.857142857142858" x2="125" y2="12.857142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5149" x1="0" y1="19.285714285714285" x2="125" y2="19.285714285714285" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5150" x1="0" y1="25.714285714285715" x2="125" y2="25.714285714285715" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5151" x1="0" y1="32.142857142857146" x2="125" y2="32.142857142857146" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5152" x1="0" y1="38.57142857142858" x2="125" y2="38.57142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5153" x1="0" y1="45.00000000000001" x2="125" y2="45.00000000000001" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line></g><g id="SvgjsG5145" class="apexcharts-gridlines-vertical" style="display: none;"></g><line id="SvgjsLine5155" x1="0" y1="45" x2="125" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line><line id="SvgjsLine5154" x1="0" y1="1" x2="0" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line></g><g id="SvgjsG5116" class="apexcharts-line-series apexcharts-plot-series"><g id="SvgjsG5117" class="apexcharts-series" seriesName="seriesx1" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath5120" d="M 0 31.5C 4.861111111111111 31.5 9.027777777777779 39.214285714285715 13.88888888888889 39.214285714285715C 18.75 39.214285714285715 22.916666666666668 21.857142857142858 27.77777777777778 21.857142857142858C 32.63888888888889 21.857142857142858 36.80555555555556 37.285714285714285 41.66666666666667 37.285714285714285C 46.52777777777778 37.285714285714285 50.69444444444445 16.714285714285715 55.55555555555556 16.714285714285715C 60.416666666666664 16.714285714285715 64.58333333333333 28.92857142857143 69.44444444444444 28.92857142857143C 74.30555555555556 28.92857142857143 78.47222222222223 7.071428571428569 83.33333333333334 7.071428571428569C 88.19444444444446 7.071428571428569 92.36111111111111 18.642857142857142 97.22222222222223 18.642857142857142C 102.08333333333334 18.642857142857142 106.25 2.5714285714285694 111.11111111111111 2.5714285714285694C 115.97222222222223 2.5714285714285694 120.1388888888889 28.92857142857143 125.00000000000001 28.92857142857143" fill="none" fill-opacity="1" stroke="rgba(226,160,63,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMaskfbhinuds)" filter="url(#SvgjsFilter5121)" pathTo="M 0 31.5C 4.861111111111111 31.5 9.027777777777779 39.214285714285715 13.88888888888889 39.214285714285715C 18.75 39.214285714285715 22.916666666666668 21.857142857142858 27.77777777777778 21.857142857142858C 32.63888888888889 21.857142857142858 36.80555555555556 37.285714285714285 41.66666666666667 37.285714285714285C 46.52777777777778 37.285714285714285 50.69444444444445 16.714285714285715 55.55555555555556 16.714285714285715C 60.416666666666664 16.714285714285715 64.58333333333333 28.92857142857143 69.44444444444444 28.92857142857143C 74.30555555555556 28.92857142857143 78.47222222222223 7.071428571428569 83.33333333333334 7.071428571428569C 88.19444444444446 7.071428571428569 92.36111111111111 18.642857142857142 97.22222222222223 18.642857142857142C 102.08333333333334 18.642857142857142 106.25 2.5714285714285694 111.11111111111111 2.5714285714285694C 115.97222222222223 2.5714285714285694 120.1388888888889 28.92857142857143 125.00000000000001 28.92857142857143" pathFrom="M -1 45L -1 45L 13.88888888888889 45L 27.77777777777778 45L 41.66666666666667 45L 55.55555555555556 45L 69.44444444444444 45L 83.33333333333334 45L 97.22222222222223 45L 111.11111111111111 45L 125.00000000000001 45"></path><g id="SvgjsG5118" class="apexcharts-series-markers-wrap" data:realIndex="0"><g class="apexcharts-series-markers"><circle id="SvgjsCircle5161" r="0" cx="0" cy="0" class="apexcharts-marker wujm5tsvz no-pointer-events" stroke="#ffffff" fill="#e2a03f" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g></g><g id="SvgjsG5119" class="apexcharts-datalabels" data:realIndex="0"></g></g><line id="SvgjsLine5156" x1="0" y1="0" x2="125" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine5157" x1="0" y1="0" x2="125" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG5158" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG5159" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG5160" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect5112" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect><g id="SvgjsG5142" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g><g id="SvgjsG5109" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend" style="max-height: 40px;"></div><div class="apexcharts-tooltip apexcharts-theme-dark"><div class="apexcharts-tooltip-series-group" style="order: 1;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(226, 160, 63);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-dark"><div class="apexcharts-yaxistooltip-text"></div></div></div></div>
                                                        </div>                                
                                                    </div>    
                                                    <div class="w-chart-section">
                                                        <div class="w-detail">
                                                            <p class="w-title">Total MXN</p>
                                                            <p class="w-stats">$ {{ number_format($statu['MXN'], 2) }}</p>
                                                        </div>
                                                        <div class="w-chart-render-one">
                                                            <div id="paid-visits" style="min-height: 80px;"><div id="apexchartstotalxusers" class="apexcharts-canvas apexchartstotalxusers apexcharts-theme-light" style="width: 164px; height: 80px;"><svg id="SvgjsSvg5049" width="164" height="80" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.dev" class="apexcharts-svg" xmlns:data="ApexChartsNS" transform="translate(0, 0)" style="background: transparent;"><g id="SvgjsG5051" class="apexcharts-inner apexcharts-graphical" transform="translate(40, 35)"><defs id="SvgjsDefs5050"><clipPath id="gridRectMasko3hakam7"><rect id="SvgjsRect5057" width="130" height="47" x="-3" y="-1" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><clipPath id="forecastMasko3hakam7"></clipPath><clipPath id="nonForecastMasko3hakam7"></clipPath><clipPath id="gridRectMarkerMasko3hakam7"><rect id="SvgjsRect5058" width="128" height="49" x="-2" y="-2" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fff"></rect></clipPath><filter id="SvgjsFilter5064" filterUnits="userSpaceOnUse" width="200%" height="200%" x="-50%" y="-50%"><feFlood id="SvgjsFeFlood5065" flood-color="#009688" flood-opacity="0.7" result="SvgjsFeFlood5065Out" in="SourceGraphic"></feFlood><feComposite id="SvgjsFeComposite5066" in="SvgjsFeFlood5065Out" in2="SourceAlpha" operator="in" result="SvgjsFeComposite5066Out"></feComposite><feOffset id="SvgjsFeOffset5067" dx="1" dy="3" result="SvgjsFeOffset5067Out" in="SvgjsFeComposite5066Out"></feOffset><feGaussianBlur id="SvgjsFeGaussianBlur5068" stdDeviation="3 " result="SvgjsFeGaussianBlur5068Out" in="SvgjsFeOffset5067Out"></feGaussianBlur><feMerge id="SvgjsFeMerge5069" result="SvgjsFeMerge5069Out" in="SourceGraphic"><feMergeNode id="SvgjsFeMergeNode5070" in="SvgjsFeGaussianBlur5068Out"></feMergeNode><feMergeNode id="SvgjsFeMergeNode5071" in="[object Arguments]"></feMergeNode></feMerge><feBlend id="SvgjsFeBlend5072" in="SourceGraphic" in2="SvgjsFeMerge5069Out" mode="normal" result="SvgjsFeBlend5072Out"></feBlend></filter></defs><line id="SvgjsLine5056" x1="0" y1="0" x2="0" y2="45" stroke="#b6b6b6" stroke-dasharray="3" stroke-linecap="butt" class="apexcharts-xcrosshairs" x="0" y="0" width="1" height="45" fill="#b1b9c4" filter="none" fill-opacity="0.9" stroke-width="1"></line><g id="SvgjsG5073" class="apexcharts-xaxis" transform="translate(0, 0)"><g id="SvgjsG5074" class="apexcharts-xaxis-texts-g" transform="translate(0, 4)"></g></g><g id="SvgjsG5086" class="apexcharts-grid"><g id="SvgjsG5087" class="apexcharts-gridlines-horizontal" style="display: none;"><line id="SvgjsLine5089" x1="0" y1="0" x2="124" y2="0" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5090" x1="0" y1="6.428571428571429" x2="124" y2="6.428571428571429" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5091" x1="0" y1="12.857142857142858" x2="124" y2="12.857142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5092" x1="0" y1="19.285714285714285" x2="124" y2="19.285714285714285" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5093" x1="0" y1="25.714285714285715" x2="124" y2="25.714285714285715" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5094" x1="0" y1="32.142857142857146" x2="124" y2="32.142857142857146" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5095" x1="0" y1="38.57142857142858" x2="124" y2="38.57142857142858" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line><line id="SvgjsLine5096" x1="0" y1="45.00000000000001" x2="124" y2="45.00000000000001" stroke="#e0e0e0" stroke-dasharray="0" stroke-linecap="butt" class="apexcharts-gridline"></line></g><g id="SvgjsG5088" class="apexcharts-gridlines-vertical" style="display: none;"></g><line id="SvgjsLine5098" x1="0" y1="45" x2="124" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line><line id="SvgjsLine5097" x1="0" y1="1" x2="0" y2="45" stroke="transparent" stroke-dasharray="0" stroke-linecap="butt"></line></g><g id="SvgjsG5059" class="apexcharts-line-series apexcharts-plot-series"><g id="SvgjsG5060" class="apexcharts-series" seriesName="seriesx1" data:longestSeries="true" rel="1" data:realIndex="0"><path id="SvgjsPath5063" d="M 0 37.28571428571429C 4.822222222222222 37.28571428571429 8.955555555555556 39.214285714285715 13.777777777777777 39.214285714285715C 18.599999999999998 39.214285714285715 22.73333333333333 32.142857142857146 27.555555555555554 32.142857142857146C 32.37777777777777 32.142857142857146 36.511111111111106 21.214285714285715 41.33333333333333 21.214285714285715C 46.15555555555555 21.214285714285715 50.288888888888884 30.85714285714286 55.11111111111111 30.85714285714286C 59.93333333333333 30.85714285714286 64.06666666666666 23.142857142857146 68.88888888888889 23.142857142857146C 73.71111111111111 23.142857142857146 77.84444444444443 29.571428571428573 82.66666666666666 29.571428571428573C 87.48888888888888 29.571428571428573 91.62222222222222 16.071428571428577 96.44444444444444 16.071428571428577C 101.26666666666667 16.071428571428577 105.39999999999999 25.071428571428573 110.22222222222221 25.071428571428573C 115.04444444444444 25.071428571428573 119.17777777777776 7.0714285714285765 123.99999999999999 7.0714285714285765" fill="none" fill-opacity="1" stroke="rgba(0,150,136,0.85)" stroke-opacity="1" stroke-linecap="butt" stroke-width="2" stroke-dasharray="0" class="apexcharts-line" index="0" clip-path="url(#gridRectMasko3hakam7)" filter="url(#SvgjsFilter5064)" pathTo="M 0 37.28571428571429C 4.822222222222222 37.28571428571429 8.955555555555556 39.214285714285715 13.777777777777777 39.214285714285715C 18.599999999999998 39.214285714285715 22.73333333333333 32.142857142857146 27.555555555555554 32.142857142857146C 32.37777777777777 32.142857142857146 36.511111111111106 21.214285714285715 41.33333333333333 21.214285714285715C 46.15555555555555 21.214285714285715 50.288888888888884 30.85714285714286 55.11111111111111 30.85714285714286C 59.93333333333333 30.85714285714286 64.06666666666666 23.142857142857146 68.88888888888889 23.142857142857146C 73.71111111111111 23.142857142857146 77.84444444444443 29.571428571428573 82.66666666666666 29.571428571428573C 87.48888888888888 29.571428571428573 91.62222222222222 16.071428571428577 96.44444444444444 16.071428571428577C 101.26666666666667 16.071428571428577 105.39999999999999 25.071428571428573 110.22222222222221 25.071428571428573C 115.04444444444444 25.071428571428573 119.17777777777776 7.0714285714285765 123.99999999999999 7.0714285714285765" pathFrom="M -1 51.42857142857143L -1 51.42857142857143L 13.777777777777777 51.42857142857143L 27.555555555555554 51.42857142857143L 41.33333333333333 51.42857142857143L 55.11111111111111 51.42857142857143L 68.88888888888889 51.42857142857143L 82.66666666666666 51.42857142857143L 96.44444444444444 51.42857142857143L 110.22222222222221 51.42857142857143L 123.99999999999999 51.42857142857143"></path><g id="SvgjsG5061" class="apexcharts-series-markers-wrap" data:realIndex="0"><g class="apexcharts-series-markers"><circle id="SvgjsCircle5104" r="0" cx="0" cy="0" class="apexcharts-marker wzs7ugkk2 no-pointer-events" stroke="#ffffff" fill="#009688" fill-opacity="1" stroke-width="2" stroke-opacity="0.9" default-marker-size="0"></circle></g></g></g><g id="SvgjsG5062" class="apexcharts-datalabels" data:realIndex="0"></g></g><line id="SvgjsLine5099" x1="0" y1="0" x2="124" y2="0" stroke="#b6b6b6" stroke-dasharray="0" stroke-width="1" stroke-linecap="butt" class="apexcharts-ycrosshairs"></line><line id="SvgjsLine5100" x1="0" y1="0" x2="124" y2="0" stroke-dasharray="0" stroke-width="0" stroke-linecap="butt" class="apexcharts-ycrosshairs-hidden"></line><g id="SvgjsG5101" class="apexcharts-yaxis-annotations"></g><g id="SvgjsG5102" class="apexcharts-xaxis-annotations"></g><g id="SvgjsG5103" class="apexcharts-point-annotations"></g></g><rect id="SvgjsRect5055" width="0" height="0" x="0" y="0" rx="0" ry="0" opacity="1" stroke-width="0" stroke="none" stroke-dasharray="0" fill="#fefefe"></rect><g id="SvgjsG5085" class="apexcharts-yaxis" rel="0" transform="translate(-18, 0)"></g><g id="SvgjsG5052" class="apexcharts-annotations"></g></svg><div class="apexcharts-legend" style="max-height: 40px;"></div><div class="apexcharts-tooltip apexcharts-theme-dark"><div class="apexcharts-tooltip-series-group" style="order: 1;"><span class="apexcharts-tooltip-marker" style="background-color: rgb(0, 150, 136);"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label"></span><span class="apexcharts-tooltip-text-y-value"></span></div><div class="apexcharts-tooltip-goals-group"><span class="apexcharts-tooltip-text-goals-label"></span><span class="apexcharts-tooltip-text-goals-value"></span></div><div class="apexcharts-tooltip-z-group"><span class="apexcharts-tooltip-text-z-label"></span><span class="apexcharts-tooltip-text-z-value"></span></div></div></div></div><div class="apexcharts-yaxistooltip apexcharts-yaxistooltip-0 apexcharts-yaxistooltip-left apexcharts-theme-dark"><div class="apexcharts-yaxistooltip-text"></div></div></div></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>            
                                        </div>
                                    @endforeach
                                @endif

                                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-activity-four">
                                        <div class="widget-heading">
                                            <h5 class="">Reservas del més</h5>
                                        </div>
                                        <div class="widget-content">
                                            <div class="mt-container-ra mx-auto bookings_day">
                                                <div class="timeline-line">
                                                    @if ( isset($bookings_month['bookings']) )
                                                        @for ($i = 0; $i < 15; $i++)
                                                            @php
                                                                // dump($booking);
                                                                $booking = ( isset($bookings_month['bookings'][$i]) ) ? $bookings_month['bookings'][$i] : [] ;
                                                            @endphp
                                                            @if ( !empty($booking) )
                                                                <div class="item-timeline timeline-<?= ( $booking->status == "CONFIRMED" ? "success" : ( $booking->status == "PENDING" ? "warning" : "danger" ) ) ?>">
                                                                    <div class="t-dot" data-original-title="" title="">
                                                                    </div>
                                                                    <div class="t-text">
                                                                        <p><span>{{ $booking->full_name }}</span> <a href="{{ route('reservations.details',['id' => $booking->id]) }}">{{ $booking->reservation_codes }}</a></p>
                                                                        <span class="badge">{{ strtolower($booking->status) }}</span>
                                                                        <p class="t-time">$ {{ number_format($booking->total_sales,2) }} {{ $booking->currency }}</p>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endfor
                                                    @endif
                                                </div>
                                            </div>
                                            @if ( isset($bookings_month['bookings']) && count($bookings_month['bookings']) > 15 )
                                                {{-- <div class="tm-action-btn">
                                                    <button class="btn" data-bs-toggle="modal" data-bs-target="#bookingsMonthModal"><span>Ver todo</span> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg></button>
                                                </div> --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
                                    <div class="widget widget-chart-two">
                                        <div class="widget-heading">
                                            <h5 class="">Reservas por estatus</h5>
                                        </div>
                                        <div class="widget-content">
                                            <div id="bookingsStatusMonth" class=""></div>
                                        </div>
                                    </div>
                                </div>
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
@endsection