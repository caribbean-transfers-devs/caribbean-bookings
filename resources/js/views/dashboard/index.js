window.addEventListener("load", function(){
    try {

        getcorkThemeObject = localStorage.getItem("theme");
        getParseObject = JSON.parse(getcorkThemeObject)
        ParsedObject = getParseObject;

        const bookings_day = new PerfectScrollbar(document.querySelector('.bookings_day'));
        dashboard.actionTable($('.table-rendering'));

        var Theme = 'dark';

        Apex.tooltip = {
            theme: Theme
        }

        /**
            ==============================
            |    @Options Charts Script   |
            ==============================
        */

        /*
            =================================
                Bookings By Status Day | Options
            =================================
        */
        var optionsStatusDay = {
            chart: {
                type: 'donut',
                width: 370,
                height: 430
            },
            colors: dashboard.seriesStatusDay().colors,
            dataLabels: {
                enabled: true
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                markers: {
                    width: 10,
                    height: 10,
                    offsetX: -5,
                    offsetY: 0
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 30
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                    size: '75%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '29px',
                            fontFamily: 'Nunito, sans-serif',
                            color: undefined,
                            offsetY: -10
                        },
                        value: {
                            show: true,
                            fontSize: '26px',
                            fontFamily: 'Nunito, sans-serif',
                            color: '#0e1726',
                            offsetY: 16,
                            formatter: function (val) {
                                return val
                            }
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            color: '#888ea8',
                            fontSize: '30px',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce( function(a, b) {
                                return a + b
                                }, 0)
                            }
                        }
                    }
                    }
                }
            },
            stroke: {
                show: true,
                width: 15,
                colors: '#ffffff'
            },
            series: dashboard.seriesStatusDay().series,
            labels: dashboard.seriesStatusDay().labels,
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

        /*
            =================================
                Bookings By Status Day | Options
            =================================
        */
        var optionsStatusMonth = {
            chart: {
                type: 'donut',
                width: 370,
                height: 430
            },
            colors: dashboard.seriesStatusMonth().colors,
            dataLabels: {
                enabled: true
            },
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '14px',
                markers: {
                    width: 10,
                    height: 10,
                    offsetX: -5,
                    offsetY: 0
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 30
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                    size: '75%',
                    background: 'transparent',
                    labels: {
                        show: true,
                        name: {
                            show: true,
                            fontSize: '29px',
                            fontFamily: 'Nunito, sans-serif',
                            color: undefined,
                            offsetY: -10
                        },
                        value: {
                            show: true,
                            fontSize: '26px',
                            fontFamily: 'Nunito, sans-serif',
                            color: '#0e1726',
                            offsetY: 16,
                            formatter: function (val) {
                                return val
                            }
                        },
                        total: {
                            show: true,
                            showAlways: true,
                            label: 'Total',
                            color: '#888ea8',
                            fontSize: '30px',
                            formatter: function (w) {
                                return w.globals.seriesTotals.reduce( function(a, b) {
                                return a + b
                                }, 0)
                            }
                        }
                    }
                    }
                }
            },
            stroke: {
                show: true,
                width: 15,
                colors: '#ffffff'
            },
            series: dashboard.seriesStatusMonth().series,
            labels: dashboard.seriesStatusMonth().labels,
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

        /*
            =================================
                Bookings Analytics Month | Options
            =================================
        */
        var optionsBookingsMonth = {
            chart: {
                fontFamily: 'Nunito, sans-serif',
                height: 365,
                type: 'area',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    opacity: 0.2,
                    blur: 10,
                    left: -7,
                    top: 22
                },
                toolbar: {
                    show: false
                },
            },
            // colors: ['#1b55e2', '#e7515a'],
            colors: ['#D01317'],
            dataLabels: {
                enabled: true
            },
            // markers: {
            //     discrete: [
            //         {
            //             seriesIndex: 0,
            //             dataPointIndex: 7,
            //             fillColor: '#000',
            //             strokeColor: '#000',
            //             size: 5
            //         },
            //         {
            //             seriesIndex: 2,
            //             dataPointIndex: 11,
            //             fillColor: '#000',
            //             strokeColor: '#000',
            //             size: 4
            //         }
            //     ]
            // },
            subtitle: {
                text: 'MXN:' + ' $ ' + dashboard.number_format(dashboard.seriesBookingsMonth().MXN,2,'.',','),
                align: 'left',
                margin: 0,
                // offsetX: 100,
                // offsetY: 20,
                floating: false,
                style: {
                    fontSize: '18px',
                    color: '#0e1726'
                }
            },
            title: {
                text: 'USD:' + ' $ ' + dashboard.number_format(dashboard.seriesBookingsMonth().USD,2,'.',','),
                align: 'left',
                margin: 0,
                // offsetX: -10,
                // offsetY: 20,
                floating: false,
                style: {
                    fontSize: '18px',
                    color: '#0e1726'
                },
            },
            stroke: {
                show: true,
                curve: 'smooth',
                width: 2,
                lineCap: 'square'
            },
            //     {
            //         name: 'Expenses',
            //         data: [16800, 16800, 15500, 14800, 15500, 17000, 21000, 16000, 15000, 17000, 14000, 17000]
            //     },
            //     {
            //         name: 'Income',
            //         data: [16500, 17500, 16200, 17300, 16000, 21500, 16000, 17000, 16000, 19000, 18000, 19000]
            //     }
            // ],
            series: dashboard.seriesBookingsMonth().series,
            //labels: dashboard.seriesBookingsMonth().labels,
            xaxis: {
                type: 'datetime',
                categories: dashboard.seriesBookingsMonth().labels,
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
                    // offsetX: 0,
                    // offsetY: 5,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-xaxis-title',
                    },
                }
            },
            yaxis: {
                labels: {
                    // formatter: function(value, index) {
                    //     return (value / 1000) + 'K'
                    // },
                    offsetX: -15,
                    offsetY: 0,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-yaxis-title',
                    },
                }
            },
            grid: {
                borderColor: '#e0e6ed',
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
                    // top: -50,
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 5
                },
            },
            // legend: {
            //     position: 'top',
            //     horizontalAlign: 'right',
            //     offsetY: -50,
            //     fontSize: '16px',
            //     fontFamily: 'Quicksand, sans-serif',
            //     markers: {
            //         width: 10,
            //         height: 10,
            //         strokeWidth: 0,
            //         strokeColor: '#fff',
            //         fillColors: undefined,
            //         radius: 12,
            //         onClick: undefined,
            //         offsetX: -5,
            //         offsetY: 0
            //     },
            //     itemMargin: {
            //         horizontal: 10,
            //         vertical: 20
            //     }
            // },
            tooltip: {
                theme: Theme,
                marker: {
                    show: true,
                },
                x: {
                    show: true,
                    format: 'dd/MM/yy'
                },
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    console.log( series, seriesIndex, dataPointIndex, w );
                    const details = dashboard.seriesBookingsMonth().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
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
            responsive: [
                {
                    breakpoint: 575,
                    options: {
                        legend: {
                            offsetY: -50,
                        },
                    },
                }
            ]
        }

        /*
            =================================
                Bookings Analytics Currency Month | Options
            =================================
        */
        var optionsBookingsCurrencyMonth = {
            chart: {
                fontFamily: 'Nunito, sans-serif',
                height: 365,
                type: 'area',
                zoom: {
                    enabled: false
                },
                dropShadow: {
                    enabled: true,
                    opacity: 0.2,
                    blur: 10,
                    left: -7,
                    top: 22
                },
                toolbar: {
                    show: false
                },
            },
            colors: ['#D01317', '#16161D'],
            dataLabels: {
                enabled: false
            },
            markers: {
                discrete: [
                    {
                        seriesIndex: 0,
                        dataPointIndex: 7,
                        fillColor: '#000',
                        strokeColor: '#000',
                        size: 5
                    },
                    {
                        seriesIndex: 2,
                        dataPointIndex: 11,
                        fillColor: '#000',
                        strokeColor: '#000',
                        size: 4
                    }
                ]
            },        
            stroke: {
                show: true,
                curve: 'smooth',
                width: 2,
                lineCap: 'square'
            },
            series: dashboard.seriesBookingsCurrencyMonth().series,
            labels: dashboard.seriesBookingsCurrencyMonth().labels,
            xaxis: {
                type: 'datetime',
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
                    // offsetX: 0,
                    // offsetY: 5,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-xaxis-title',
                    },
                }
            },
            yaxis: {
                labels: {
                    formatter: function(value, index) {
                        return (value / 1000) + 'K'
                    },
                    offsetX: -15,
                    offsetY: 0,
                    style: {
                        fontSize: '12px',
                        fontFamily: 'Nunito, sans-serif',
                        cssClass: 'apexcharts-yaxis-title',
                    },
                }
            },
            grid: {
                borderColor: '#e0e6ed',
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
                    // top: -50,
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 5
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                // offsetY: -50,
                fontSize: '16px',
                fontFamily: 'Quicksand, sans-serif',
                markers: {
                    width: 10,
                    height: 10,
                    strokeWidth: 0,
                    strokeColor: '#fff',
                    fillColors: undefined,
                    radius: 12,
                    onClick: undefined,
                    offsetX: -5,
                    offsetY: 0
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 20
                }
            },
            tooltip: {
                theme: Theme,
                marker: {
                    show: true,
                },
                x: {
                    show: false,
                },
                // custom: function({ series, seriesIndex, dataPointIndex, w }) {
                //     console.log( series, seriesIndex, dataPointIndex, w );
                //     const usd = dashboard.seriesBookingsCurrencyMonth().series[0].data[dataPointIndex];
                //     const mxn = dashboard.seriesBookingsCurrencyMonth().series[1].data[dataPointIndex];
                //     console.log(usd, mxn);
                //     // return `<div class="custom-tooltip">
                //     //             <div class="apexcharts-tooltip-y-group">
                //     //                 <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                //     //                 <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                //     //             </div>
                //     //             <div class="apexcharts-tooltip-y-group">
                //     //                 <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                //     //                 <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                //     //             </div>
                //     //         </div>`;
                // }                
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
            responsive: [
                {
                    breakpoint: 575,
                    options: {
                        legend: {
                            offsetY: -50,
                        },
                    },
                }
            ]
        }

        /*
            =================================
                Bookings Analytics Sites Month | Options
            =================================
        */
        var optionsBookingsSitesMonth = {
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
            colors: ['#D01317'],
            dataLabels: {
                enabled: true
            },            
            series: dashboard.seriesSitesMonth().series,
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
                categories: dashboard.seriesSitesMonth().labels
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
                theme: Theme,
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = dashboard.seriesSitesMonth().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }                
            },            
            responsive: [{
                breakpoint: 575,
            }],
        }

        /*
            =================================
                Bookings Analytics Sites Day | Options
            =================================
        */
        var optionsBookingsSitesDay = {
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
            colors: ['#D01317'],
            dataLabels: {
                enabled: true
            },            
            series: dashboard.seriesSitesDay().series,
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
                categories: dashboard.seriesSitesDay().labels
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
                theme: Theme,
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = dashboard.seriesSitesDay().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }
            },            
            responsive: [{
                breakpoint: 575,
            }],
        }        

        /*
            =================================
                Bookings Analytics Destinations Day | Options
            =================================
        */        
        var optionsBookingsDestinationsDay = {
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
            // colors: dashboard.seriesDestinationMonth().colors,
            colors: ['#16161D'],
            dataLabels: {
                enabled: true,
            },
            series: dashboard.seriesDestinationDay().series,
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
                categories: dashboard.seriesDestinationDay().labels,
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
                theme: Theme,
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = dashboard.seriesDestinationDay().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }                
            },
            responsive: [{
                breakpoint: 575,
            }]
        }

        /*
            =================================
                Bookings Analytics Destinations Month | Options
            =================================
        */        
        var optionsBookingsDestinationsMonth = {
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
            // colors: dashboard.seriesDestinationMonth().colors,
            colors: ['#16161D'],
            dataLabels: {
                enabled: true,
            },
            series: dashboard.seriesDestinationMonth().series,
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
                categories: dashboard.seriesDestinationMonth().labels,
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
                theme: Theme,
                custom: function({ series, seriesIndex, dataPointIndex, w }) {
                    const details = dashboard.seriesDestinationMonth().series[seriesIndex].data[dataPointIndex].details;
                    return `<div class="custom-tooltip">
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                                </div>
                                <div class="apexcharts-tooltip-y-group">
                                    <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                    <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                                </div>
                            </div>`;
                }                
            },
            responsive: [{
                breakpoint: 575,
            }]
        }        

        /**
             ==============================
            |    @Render Charts Script    |
            ==============================
        */

        /*
            =================================
                Bookings By Status Day | Render
            =================================
        */
        var bookingsStatusDay = new ApexCharts(
            document.querySelector("#bookingsStatusDay"),
            optionsStatusDay
        );
        bookingsStatusDay.render();

        /*
            =================================
                Bookings By Status Month | Render
            =================================
        */
        var bookingsStatusMonth = new ApexCharts(
            document.querySelector("#bookingsStatusMonth"),
            optionsStatusMonth
        );
        bookingsStatusMonth.render();

        /*
            ================================
                Bookings Analytics Month | Render
            ================================
        */
        var bookingsAnalyticsMonth = new ApexCharts(
            document.querySelector("#bookingsAnalyticsMonth"),
            optionsBookingsMonth
        );
        bookingsAnalyticsMonth.render();

        /*
            ================================
                Bookings Analytics Currency Month | Render
            ================================
        */
        var bookingsAnalyticsCurrencyMonth = new ApexCharts(
            document.querySelector("#bookingsAnalyticsCurrencyMonth"),
            optionsBookingsCurrencyMonth
        );
        bookingsAnalyticsCurrencyMonth.render();

        /*
            ================================
                Bookings Analytics Sites Day | Render
            ================================
        */
        var bookingsAnalyticsSitesDay = new ApexCharts(
            document.querySelector("#bookingsAnalyticsSitesDay"),
            optionsBookingsSitesDay
        );
        bookingsAnalyticsSitesDay.render();

        /*
            ================================
                Bookings Analytics Sites Month | Render
            ================================
        */
        var bookingsAnalyticsSitesMonth = new ApexCharts(
            document.querySelector("#bookingsAnalyticsSitesMonth"),
            optionsBookingsSitesMonth
        );
        bookingsAnalyticsSitesMonth.render();

        /*
            ================================
                Bookings Analytics Destinations Day | Render
            ================================
        */
        var bookingsAnalyticsDestinationsDay = new ApexCharts(
            document.querySelector("#bookingsAnalyticsDestinationsDay"),
            optionsBookingsDestinationsDay
        );
        bookingsAnalyticsDestinationsDay.render();

        /*
            ================================
                Bookings Analytics Destinations Month | Render
            ================================
        */
        var bookingsAnalyticsDestinationsMonth = new ApexCharts(
            document.querySelector("#bookingsAnalyticsDestinationsMonth"),
            optionsBookingsDestinationsMonth
        );
        bookingsAnalyticsDestinationsMonth.render();

        /**
         * =================================================================================================
         * |     @Re_Render | Re render all the necessary JS when clicked to switch/toggle theme           |
         * =================================================================================================
        */

        if( document.querySelector('.theme-toggle') != null ){
            document.querySelector('.theme-toggle').addEventListener('click', function() {

                getcorkThemeObject = localStorage.getItem("theme");
                getParseObject = JSON.parse(getcorkThemeObject)
                ParsedObject = getParseObject;

                    /*
                    ==================================
                        Bookings By Status Day | Options
                    ==================================
                    */

                    bookingsStatusDay.updateOptions({
                        stroke: {
                            colors: ( ParsedObject.settings.layout.darkMode ? '#0e1726' : '#ffffff' )
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        value: {
                                            color: ( ParsedObject.settings.layout.darkMode ? '#bfc9d4' : '#0e1726' )
                                        }
                                    }
                                }
                            }
                        }
                    });

                    /*
                    ==================================
                        Bookings By Status Month | Options
                    ==================================
                    */

                    bookingsStatusMonth.updateOptions({
                        stroke: {
                            colors: ( ParsedObject.settings.layout.darkMode ? '#0e1726' : '#ffffff' )
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        value: {
                                            color: ( ParsedObject.settings.layout.darkMode ? '#bfc9d4' : '#0e1726' )
                                        }
                                    }
                                }
                            }
                        }
                    });

                    /*
                        =================================
                            Bookings Analytics Month | Options
                        =================================
                    */
                    bookingsAnalyticsMonth.updateOptions({
                        subtitle: {
                            style: {
                                color: ( ParsedObject.settings.layout.darkMode ? '#e0e6ed' : '#0e1726' )
                            }
                        },
                        title: {
                            style: {
                                color: ( ParsedObject.settings.layout.darkMode ? '#e0e6ed' : '#0e1726' )
                            },
                        },                    
                    });

            });
        }
    } catch(e) {
        console.log(e);
    }
})