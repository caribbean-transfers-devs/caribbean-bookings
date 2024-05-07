@extends('layout.master')
@section('title') Admin Dashboard @endsection

@php
    $day_data = [
        "total" => 0,
        "USD" => 0,
        "MXN" => 0
    ];
@endphp

@php
    // dump($bookings_destinations_month);
@endphp

@push('up-stack')
    <link href="{{ mix('/assets/css/dashboards/admin.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/dashboards/admin.min.css') }}" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.5/css/perfect-scrollbar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.49.0/apexcharts.min.css">
    <style>
        .content{
            padding: 1.5rem 1.5rem .75rem;
        }
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

@push('bootom-stack')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/1.5.5/perfect-scrollbar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.49.0/apexcharts.min.js"></script>
    <script>
        const bookings_day = new PerfectScrollbar(document.querySelector('.mt-container-ra'));

        let apexcharts = {
            dataMonth: @json($bookings_month),
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
            seriesMonth: function() {
                let seriesMonth = {
                    USD: 0,
                    MXN: 0,
                    series: [{
                        name: "Ventas",
                        data: []
                    }],
                    labels: []
                };            
                const data = Object.entries(this.dataMonth);
                data.forEach( ([date, dataDay]) => {
                    let dateOriginal = date.split('-');
                    // let dateFormat = dateOriginal[2] + '-' + dateOriginal[1] + '-' + dateOriginal[0];
                    let dateFormat = dateOriginal[2] + '/' + dateOriginal[1];
                    seriesMonth.USD  = seriesMonth.USD + dataDay.USD;
                    seriesMonth.MXN  = seriesMonth.MXN + dataDay.MXN;
                    seriesMonth.series[0].data.push({                        
                        y: dataDay.counter,
                        x: dateFormat,
                        details: dataDay
                    });
                    seriesMonth.labels.push(dateFormat);
                });
                return seriesMonth;
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

        console.log(apexcharts.seriesDestinationMonth());

        /*
            =================================
                Sales | Render
            =================================
        */        
        var options2 = {
            chart: {
                fontFamily: 'Nunito, sans-serif',
                height: 370,
                type: 'area',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
                dropShadow: {
                    enabled: true,
                    opacity: 0.2,
                    blur: 10,
                    left: -7,
                    top: 22
                },
            },
            dataLabels: {
                enabled: true,
            },
            title: {
                text: 'MXN: ' + ' $ ' + apexcharts.number_format(apexcharts.seriesMonth().MXN,2,'.',','),
                align: 'left',
                margin: 0,
                floating: false,
                style: {
                    fontSize: '18px',
                    fontWeight:  'normal',
                    color:  '#000'
                },
            },
            subtitle: {
                text: 'USD: ' + ' $ ' + apexcharts.number_format(apexcharts.seriesMonth().USD,2,'.',','),
                align: 'left',
                margin: 0,
                floating: false,
                style: {
                    fontSize: '18px',
                    fontWeight:  'normal',
                    color:  '#000'
                }
            },        
            stroke: {
                show: true,
                curve: 'smooth',
                width: 2,
                lineCap: 'square'
            },
            series: apexcharts.seriesMonth().series,
            labels: apexcharts.seriesMonth().labels,
            xaxis: {
                // type: "datetime",
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                crosshairs: {
                    show: true
                },
                labels: {
                    offsetX: 0,
                    offsetY: 5,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-xaxis-title',
                    },
                }
            },
            yaxis: {
                labels: {
                    offsetX: -15,
                    offsetY: 0,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-yaxis-title',
                    },
                },                
            },
            grid: {
                borderColor: '#191e3a',
                strokeDashArray: 5,
                xaxis: {
                    lines: {
                        show: true
                    }
                },   
                yaxis: {
                    lines: {
                        show: false,
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 5
                },
            },
            tooltip: {
                theme: 'light',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = apexcharts.seriesMonth().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ apexcharts.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ apexcharts.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }                
            },
            fill: {
                type:"gradient",
                gradient: {
                    type: "vertical",
                    shadeIntensity: 1,
                    inverseColors: !1,
                    opacityFrom: .19,
                    opacityTo: .05,
                    stops: [100, 100]
                }
            },
            responsive: [{
                breakpoint: 575,
            }]
        }
        let chartMonth = new ApexCharts(document.querySelector("#chartMonth"), options2);
        chartMonth.render();

        /*
            =================================
                Sales By Sites | Render
            =================================
        */
        var options = {
            chart: {
                type: 'bar',
                height: 365,
                toolbar: {
                    show: false
                },
            },
            dataLabels: {
                enabled: true
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    dataLabels: {
                        position: 'top',
                    },                    
                }
            },
            series: apexcharts.seriesSitesMonth().series,
            xaxis: {
                type: 'category',
                categories: apexcharts.seriesSitesMonth().labels
            },
            tooltip: {
                theme: 'light',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = apexcharts.seriesSitesMonth().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ apexcharts.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ apexcharts.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }                
            },            
            responsive: [
                { 
                breakpoint: 1440, options: {
                    chart: {
                    width: 325
                    },
                }
                },
                { 
                breakpoint: 1199, options: {
                    chart: {
                    width: 380
                    },
                }
                },
                { 
                breakpoint: 575, options: {
                    chart: {
                    width: 320
                    },
                }
                },
            ],
        }
        let chartMonthSites = new ApexCharts( document.querySelector("#chartMonthSites"), options );
        chartMonthSites.render();

        /*
            =================================
                Sales By Destinations | Render
            =================================
        */        
        var options3 = {
            chart: {
                fontFamily: 'Nunito, sans-serif',
                height: 370,
                type: 'bar',
                zoom: {
                    enabled: false
                },
                toolbar: {
                    show: false
                }
            },
            // colors: apexcharts.seriesDestinationMonth().colors,
            dataLabels: {
                enabled: true,
            },
            title: {
                text: 'MXN: ' + ' $ ' + apexcharts.number_format(apexcharts.seriesDestinationMonth().MXN,2,'.',','),
                align: 'left',
                margin: 0,
                floating: false,
                style: {
                    fontSize: '18px',
                    fontWeight:  'normal',
                    color:  '#000'
                },
            },
            subtitle: {
                text: 'USD: ' + ' $ ' + apexcharts.number_format(apexcharts.seriesDestinationMonth().USD,2,'.',','),
                align: 'left',
                margin: 0,
                floating: false,
                style: {
                    fontSize: '18px',
                    fontWeight:  'normal',
                    color:  '#000'
                }
            },        
            series: apexcharts.seriesDestinationMonth().series,
            xaxis: {
                type: 'category',
                labels: {
                    offsetX: 0,
                    offsetY: 5,
                    rotate: -45,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-xaxis-title',
                    },
                },
                categories: apexcharts.seriesDestinationMonth().labels,
            },
            yaxis: {
                labels: {
                    offsetX: -15,
                    offsetY: 0,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-yaxis-title',
                    },
                },                
            },
            tooltip: {
                theme: 'light',
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = apexcharts.seriesDestinationMonth().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ apexcharts.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ apexcharts.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }                
            },
            responsive: [{
                breakpoint: 575,
            }]
        }
        let chartMonthDestinations = new ApexCharts(document.querySelector("#chartMonthDestinations"), options3);
        chartMonthDestinations.render();        

        $(function() {
            const picker = new easepick.create({
                element: "#lookup_date",        
                css: [
                    'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
                    'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
                ],
                zIndex: 10,
                plugins: ['RangePlugin'],
            })
        });
    </script>
@endpush

@section('content')
    <h1 class="h3 mb-3 button_">
        Reporte de ventas
        <a href="#" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filterModal">Filtrar</a>  
    </h1>
    <div class="row">
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="widget">
                <div class="widget-heading">
                    <h5>Resumen de reservas del día</h5>
                </div>
                <div class="widget-content">
                    <div class="mt-container-ra mx-auto">
                        <div class="timeline-line">
                            @foreach($bookings_day as $key => $booking)
                                @php
                                    $day_data['total'] += $booking['counter'];
                                    $day_data['USD'] += $booking['USD'];
                                    $day_data['MXN'] += $booking['MXN'];
                                @endphp                            
                                {{-- <tr>
                                    <td>{{ $key }}</td>
                                    <td class="text-center">{{ $value['counter'] }}</td>
                                    <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                    <td class="text-end">{{ number_format($value['MXN']) }}</td>
                                </tr> --}}

                                @foreach($booking['items'] as $item)
                                    {{-- @dump($item); --}}
                                    <div class="item-timeline timeline-{{ ( $item->status == "CONFIRMED" ? "success" : "warning" ) }}">
                                        <div class="t-dot" data-original-title="" title=""></div>
                                        <div class="t-text">
                                            <p><span>{{ $item->client_full_name }}</span> - {{ $item->destination_name }} - <a href="javascript:void(0);">{{ $item->reservation_codes }}</a></p>
                                            <span class="badge">{{ $item->status }}</span>
                                            <p class="t-time">$ {{ number_format($item->total_sales,2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                        </div> 
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-wallet-one">
                <div class="wallet-info text-center mb-3">
                    <p class="wallet-title mb-3">Balance del día</p>
                    <p class="total-amount mb-3">{{ $day_data['total'] }}</p>
                </div>
                <hr>
                <ul class="list-group list-group-media">
                    <li class="list-group-item ">
                        <div class="media">
                            <div class="media-body">
                                <h6 class="tx-inverse">$ {{ number_format($day_data['USD'],2) }}</h6>
                                {{-- <p class="mg-b-0">June 6, 10:34</p> --}}
                                <p class="amount">USD</p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item">
                        <div class="media">
                            <div class="media-body">
                                <h6 class="tx-inverse">$ {{ number_format($day_data['MXN'],2) }}</h6>
                                {{-- <p class="mg-b-0">June 14, 05:21</p> --}}
                                <p class="amount">MXN</p>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <h5 class="">Resumen de ventas del més</h5>
                </div>

                <div class="widget-content">
                    <div id="chartMonth"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-two">
                <div class="widget-heading">
                    <h5 class="">Resumen de ventas por sitio</h5>
                </div>
                <div class="widget-content">
                    <div id="chartMonthSites" class=""></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <h5 class="">Resumen de ventas por destino</h5>
                </div>

                <div class="widget-content">
                    <div id="chartMonthDestinations"></div>
                </div>
            </div>
        </div>
    </div>

    <x-modals.reservations.reports :data="$data" />
@endsection