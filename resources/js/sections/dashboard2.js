if ( document.getElementById('lookup_date') != null ) {
    const picker = new easepick.create({
        element: "#lookup_date",
        css: [
            'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
            'https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.css',
        ],
        zIndex: 10,
    });
}

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
                Bookings Analytics Currency Month | Options
            =================================
        */
        // var optionsBookingsCurrencyMonth = {
        //     chart: {
        //         fontFamily: 'Nunito, sans-serif',
        //         height: 365,
        //         type: 'area',
        //         zoom: {
        //             enabled: false
        //         },
        //         dropShadow: {
        //             enabled: true,
        //             opacity: 0.2,
        //             blur: 10,
        //             left: -7,
        //             top: 22
        //         },
        //         toolbar: {
        //             show: false
        //         },
        //     },
        //     colors: ['#D01317', '#16161D'],
        //     dataLabels: {
        //         enabled: true,
        //         formatter: function (value, { seriesIndex, dataPointIndex, w }) {
        //             return dashboard.number_format(value,2,'.',',')
        //         },
        //     },
        //     markers: {
        //         discrete: [
        //             {
        //                 seriesIndex: 0,
        //                 dataPointIndex: 7,
        //                 fillColor: '#000',
        //                 strokeColor: '#000',
        //                 size: 5
        //             },
        //             {
        //                 seriesIndex: 2,
        //                 dataPointIndex: 11,
        //                 fillColor: '#000',
        //                 strokeColor: '#000',
        //                 size: 4
        //             }
        //         ]
        //     },        
        //     stroke: {
        //         show: true,
        //         curve: 'smooth',
        //         width: 2,
        //         lineCap: 'square'
        //     },
        //     series: dashboard.seriesBookingsCurrencyMonth().series,
        //     labels: dashboard.seriesBookingsCurrencyMonth().labels,
        //     xaxis: {
        //         type: 'datetime',
        //         axisBorder: {
        //             show: false
        //         },
        //         axisTicks: {
        //             show: false
        //         },
        //         crosshairs: {
        //             show: true
        //         },
        //         labels: {
        //             // offsetX: 0,
        //             // offsetY: 5,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-xaxis-title',
        //             },
        //         }
        //     },
        //     yaxis: {
        //         labels: {
        //             formatter: function(value, index) {
        //                 return (value / 1000) + 'K'
        //             },
        //             offsetX: -15,
        //             offsetY: 0,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-yaxis-title',
        //             },
        //         }
        //     },
        //     grid: {
        //         borderColor: '#e0e6ed',
        //         strokeDashArray: 5,
        //         xaxis: {
        //             lines: {
        //                 show: true
        //             }
        //         },
        //         yaxis: {
        //             lines: {
        //                 show: false,
        //             }
        //         },
        //         padding: {
        //             // top: -50,
        //             top: 0,
        //             right: 0,
        //             bottom: 0,
        //             left: 5
        //         },
        //     },
        //     legend: {
        //         position: 'top',
        //         horizontalAlign: 'right',
        //         // offsetY: -50,
        //         fontSize: '16px',
        //         fontFamily: 'Quicksand, sans-serif',
        //         markers: {
        //             width: 10,
        //             height: 10,
        //             strokeWidth: 0,
        //             strokeColor: '#fff',
        //             fillColors: undefined,
        //             radius: 12,
        //             onClick: undefined,
        //             offsetX: -5,
        //             offsetY: 0
        //         },
        //         itemMargin: {
        //             horizontal: 10,
        //             vertical: 20
        //         }
        //     },
        //     tooltip: {
        //         theme: Theme,
        //         marker: {
        //             show: true,
        //         },
        //         x: {
        //             show: false,
        //         },
        //         custom: function({ series, seriesIndex, dataPointIndex, w }) {
        //             // console.log( series, seriesIndex, dataPointIndex, w );
        //             // // console.log( series[seriesIndex][dataPointIndex] );
        //             // console.log(w.config.series[0].name + ":  " + w.config.series[0].data[dataPointIndex]);
        //             // console.log(w.config.series[1].name + ":  " + w.config.series[0].data[dataPointIndex]);
        //             return `<div class="custom-tooltip">
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(series[0][dataPointIndex],2,'.',',') }</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(series[1][dataPointIndex],2,'.',',') }</span>
        //                         </div>
        //                     </div>`;
        //         }                
        //     },
        //     fill: {
        //         type:"gradient",
        //         gradient: {
        //             type: "vertical",
        //             shadeIntensity: 1,
        //             inverseColors: !1,
        //             opacityFrom: .19,
        //             opacityTo: .05,
        //             stops: [100, 100]
        //         }
        //     },
        //     responsive: [
        //         {
        //             breakpoint: 575,
        //             options: {
        //                 legend: {
        //                     offsetY: -50,
        //                 },
        //             },
        //         }
        //     ]
        // }

        /*
            =================================
                Bookings Analytics Sites Month | Options
            =================================
        */
        // var optionsBookingsSitesMonth = {
        //     chart: {
        //         fontFamily: 'Nunito, sans-serif',
        //         height: 370,
        //         type: 'bar',
        //         zoom: {
        //             enabled: false
        //         },
        //         toolbar: {
        //             show: false
        //         }                
        //     },
        //     colors: ['#D01317'],
        //     dataLabels: {
        //         enabled: true
        //     },            
        //     series: dashboard.seriesSitesMonth().series,
        //     xaxis: {
        //         type: 'category',
        //         labels: {
        //             offsetX: 0,
        //             offsetY: 5,
        //             rotate: -45,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-xaxis-title',
        //             },
        //         },                
        //         categories: dashboard.seriesSitesMonth().labels
        //     },
        //     yaxis: {
        //         labels: {
        //             offsetX: -15,
        //             offsetY: 0,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-yaxis-title',
        //             },
        //         },                
        //     },            
        //     tooltip: {
        //         theme: Theme,
        //         custom: function({ series, seriesIndex, dataPointIndex, w }) {
        //             const details = dashboard.seriesSitesMonth().series[seriesIndex].data[dataPointIndex].details;
        //             return `<div class="custom-tooltip">
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
        //                         </div>
        //                     </div>`;
        //         }                
        //     },            
        //     responsive: [{
        //         breakpoint: 575,
        //     }],
        // }

        /*
            =================================
                Bookings Analytics Sites Day | Options
            =================================
        */
        // var optionsBookingsSitesDay = {
        //     chart: {
        //         fontFamily: 'Nunito, sans-serif',
        //         height: 370,
        //         type: 'bar',
        //         zoom: {
        //             enabled: false
        //         },
        //         toolbar: {
        //             show: false
        //         }                
        //     },
        //     colors: ['#D01317'],
        //     dataLabels: {
        //         enabled: true
        //     },            
        //     series: dashboard.seriesSitesDay().series,
        //     xaxis: {
        //         type: 'category',
        //         labels: {
        //             offsetX: 0,
        //             offsetY: 5,
        //             rotate: -45,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-xaxis-title',
        //             },
        //         },                
        //         categories: dashboard.seriesSitesDay().labels
        //     },
        //     yaxis: {
        //         labels: {
        //             offsetX: -15,
        //             offsetY: 0,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-yaxis-title',
        //             },
        //         },                
        //     },            
        //     tooltip: {
        //         theme: Theme,
        //         custom: function({ series, seriesIndex, dataPointIndex, w }) {
        //             const details = dashboard.seriesSitesDay().series[seriesIndex].data[dataPointIndex].details;
        //             return `<div class="custom-tooltip">
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
        //                         </div>                                
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
        //                         </div>
        //                     </div>`;
        //         }
        //     },            
        //     responsive: [{
        //         breakpoint: 575,
        //     }],
        // }

        /*
            =================================
                Bookings Analytics Destinations Day | Options
            =================================
        */        
        // var optionsBookingsDestinationsDay = {
        //     chart: {
        //         fontFamily: 'Nunito, sans-serif',
        //         height: 370,
        //         type: 'bar',
        //         zoom: {
        //             enabled: false
        //         },
        //         toolbar: {
        //             show: false
        //         }
        //     },
        //     // colors: dashboard.seriesDestinationMonth().colors,
        //     colors: ['#16161D'],
        //     dataLabels: {
        //         enabled: true,
        //     },
        //     series: dashboard.seriesDestinationDay().series,
        //     xaxis: {
        //         type: 'category',
        //         labels: {
        //             offsetX: 0,
        //             offsetY: 5,
        //             rotate: -45,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-xaxis-title',
        //             },
        //         },
        //         categories: dashboard.seriesDestinationDay().labels,
        //     },
        //     yaxis: {
        //         labels: {
        //             offsetX: -15,
        //             offsetY: 0,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-yaxis-title',
        //             },
        //         },                
        //     },
        //     tooltip: {
        //         theme: Theme,
        //         custom: function({ series, seriesIndex, dataPointIndex, w }) {
        //             const details = dashboard.seriesDestinationDay().series[seriesIndex].data[dataPointIndex].details;
        //             return `<div class="custom-tooltip">
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
        //                         </div>
        //                     </div>`;
        //         }                
        //     },
        //     responsive: [{
        //         breakpoint: 575,
        //     }]
        // }

        /*
            =================================
                Bookings Analytics Destinations Month | Options
            =================================
        */        
        // var optionsBookingsDestinationsMonth = {
        //     chart: {
        //         fontFamily: 'Nunito, sans-serif',
        //         height: 370,
        //         type: 'bar',
        //         zoom: {
        //             enabled: false
        //         },
        //         toolbar: {
        //             show: false
        //         }
        //     },
        //     // colors: dashboard.seriesDestinationMonth().colors,
        //     colors: ['#16161D'],
        //     dataLabels: {
        //         enabled: true,
        //     },
        //     series: dashboard.seriesDestinationMonth().series,
        //     xaxis: {
        //         type: 'category',
        //         labels: {
        //             offsetX: 0,
        //             offsetY: 5,
        //             rotate: -45,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-xaxis-title',
        //             },
        //         },
        //         categories: dashboard.seriesDestinationMonth().labels,
        //     },
        //     yaxis: {
        //         labels: {
        //             offsetX: -15,
        //             offsetY: 0,
        //             style: {
        //                 fontSize: '12px',
        //                 fontFamily: 'Nunito, sans-serif',
        //                 cssClass: 'apexcharts-yaxis-title',
        //             },
        //         },                
        //     },
        //     tooltip: {
        //         theme: Theme,
        //         custom: function({ series, seriesIndex, dataPointIndex, w }) {
        //             const details = dashboard.seriesDestinationMonth().series[seriesIndex].data[dataPointIndex].details;
        //             return `<div class="custom-tooltip">
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
        //                         </div>
        //                         <div class="apexcharts-tooltip-y-group">
        //                             <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
        //                             <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
        //                         </div>
        //                     </div>`;
        //         }                
        //     },
        //     responsive: [{
        //         breakpoint: 575,
        //     }]
        // }

        /**
             ==============================
            |    @Render Charts Script    |
            ==============================
        */

        /*
            ================================
                Bookings Analytics Currency Month | Render
            ================================
        */
        // var bookingsAnalyticsCurrencyMonth = new ApexCharts(
        //     document.querySelector("#bookingsAnalyticsCurrencyMonth"),
        //     optionsBookingsCurrencyMonth
        // );
        // bookingsAnalyticsCurrencyMonth.render();

        /*
            ================================
                Bookings Analytics Sites Day | Render
            ================================
        */
        // var bookingsAnalyticsSitesDay = new ApexCharts(
        //     document.querySelector("#bookingsAnalyticsSitesDay"),
        //     optionsBookingsSitesDay
        // );
        // bookingsAnalyticsSitesDay.render();

        /*
            ================================
                Bookings Analytics Sites Month | Render
            ================================
        */
        // var bookingsAnalyticsSitesMonth = new ApexCharts(
        //     document.querySelector("#bookingsAnalyticsSitesMonth"),
        //     optionsBookingsSitesMonth
        // );
        // bookingsAnalyticsSitesMonth.render();

        /*
            ================================
                Bookings Analytics Destinations Day | Render
            ================================
        */
        // var bookingsAnalyticsDestinationsDay = new ApexCharts(
        //     document.querySelector("#bookingsAnalyticsDestinationsDay"),
        //     optionsBookingsDestinationsDay
        // );
        // bookingsAnalyticsDestinationsDay.render();

        /*
            ================================
                Bookings Analytics Destinations Month | Render
            ================================
        */
        // var bookingsAnalyticsDestinationsMonth = new ApexCharts(
        //     document.querySelector("#bookingsAnalyticsDestinationsMonth"),
        //     optionsBookingsDestinationsMonth
        // );
        // bookingsAnalyticsDestinationsMonth.render();

        var char;
        var char2;
        var paramsSales = {
            day: {
                total: {
                    usd: "$" + dashboard.number_format(dashboard.seriesBookingsDay().USD,2,'.',','),
                    mxn: "$" + dashboard.number_format(dashboard.seriesBookingsDay().MXN,2,'.',','),
                },
                series: dashboard.seriesBookingsDay().series,
                labels: dashboard.seriesBookingsDay().labels,
            },
            month: {
                total: {
                    usd: "$" + dashboard.number_format(dashboard.seriesBookingsMonth().USD,2,'.',','),
                    mxn: "$" + dashboard.number_format(dashboard.seriesBookingsMonth().MXN,2,'.',','),
                },                
                series: dashboard.seriesBookingsMonth().series,
                labels: dashboard.seriesBookingsMonth().labels,
            }
        }

        var paramsOptions = {
            day: {
                sites:{
                    series: dashboard.seriesSitesDay().series,
                    labels: dashboard.seriesSitesDay().labels,
                },
                destinations:{
                    series: dashboard.seriesDestinationDay().series,
                    labels: dashboard.seriesDestinationDay().labels,
                }
            },
            month: {
                sites:{
                    series: dashboard.seriesSitesMonth().series,
                    labels: dashboard.seriesSitesMonth().labels,
                },
                destinations:{
                    series: dashboard.seriesDestinationMonth().series,
                    labels: dashboard.seriesDestinationMonth().labels,
                }
            }
        }        

        function renderSales(key){
            // Declaramos variables
            const __items = document.getElementById('items');
            const __links = __items.querySelectorAll('.nav-link');
            const __active = document.getElementById('item_' + key);
            const __sales_usd = document.getElementById('total_sales_usd');
            const __sales_mxn = document.getElementById('total_sales_mxn');

            // Destruir la gráfica actual si existe
            if (typeof char !== 'undefined' && char !== null) {
                char.destroy();
            }

            // Configuración para la nueva gráfica
            var options = {
                chart: {
                    fontFamily: 'Nunito, sans-serif',
                    height: 380,
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
                colors: ['#D01317'],
                dataLabels: {
                    enabled: true
                },
                stroke: {
                    show: true,
                    curve: 'smooth',
                    width: 2,
                    lineCap: 'square'
                },
                series: paramsSales[key].series,
                xaxis: {
                    type: 'datetime',
                    categories: paramsSales[key].labels,
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
                        top: 0,
                        right: 0,
                        bottom: 0,
                        left: 5
                    },
                },
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
                        // console.log( series, seriesIndex, dataPointIndex, w );
                        const details = paramsSales[key].series[seriesIndex].data[dataPointIndex].details;
                        return `<div class="custom-tooltip">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                        <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                        <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                                    </div>
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                        <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
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
            
            __links.forEach(__link => {
                __link.classList.remove('active');    
            });
            
            __active.classList.add('active');
            __sales_usd.innerHTML = paramsSales[key].total.usd;
            __sales_mxn.innerHTML = paramsSales[key].total.mxn;

            // Renderizar la nueva gráfica
            char = new ApexCharts(document.querySelector("#stacked-column-chart"), options);
            char.render();
        }

        function renderOption(key, option){
            // Declaramos variables
            const __items = document.getElementById('itemsOptions');
            const __links = __items.querySelectorAll('.nav-link');
            const __active = document.getElementById('itemOption_' + key);

            // Destruir la gráfica actual si existe
            if (typeof char2 !== 'undefined' && char2 !== null) {
                char2.destroy();
            }

            // Configuración para la nueva gráfica
            var options = {
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
                series: paramsOptions[key][option].series,
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
                    categories: paramsOptions[key][option].labels
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
                        const details = paramsOptions[key][option].series[seriesIndex].data[dataPointIndex].details;
                        return `<div class="custom-tooltip">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total de ventas:</span>
                                        <span class="apexcharts-tooltip-text-y-value">${details.counter}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total USD:</span>
                                        <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.USD,2,'.',',') }</span>
                                    </div>                                
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total MXN:</span>
                                        <span class="apexcharts-tooltip-text-y-value">${ dashboard.number_format(details.MXN,2,'.',',') }</span>
                                    </div>
                                </div>`;
                    }
                },            
                responsive: [{
                    breakpoint: 575,
                }],
            }
            
            __links.forEach(__link => {
                __link.classList.remove('active');    
            });
            
            __active.classList.add('active');

            // Renderizar la nueva gráfica
            char2 = new ApexCharts(document.querySelector("#stacked-column-chart2"), options);
            char2.render();
        }
       
        const __status = document.getElementById('status');
        if( __status != null ){
            eventData(__status);
            __status.addEventListener('change', function(event){
                event.preventDefault();
                eventData(__status);
            })
        }

        function eventData(__status){
            const __status_previous = document.querySelectorAll('.' + ( __status.value == "day" ? "month" : "day" ) + '_status');
            const __status_target = document.querySelectorAll('.' + __status.value + '_status');
            const __option = document.getElementById('option');

            //Ocultamos los div que nos corresponden a la solicitud
            if( __status_previous.length > 0 ){
                __status_previous.forEach(__previous => {
                    __previous.classList.add('d-none');
                });
            }

            //Mostrmos los div que corresponden a la solicitud
            if( __status_target.length > 0 ){
                __status_target.forEach(__target => {
                    __target.classList.remove('d-none');
                });
            }

            renderSales(__status.value);            
            renderOption(__status.value, __option.value);
        }

        const __option = document.getElementById('option');
        if( __option != null ){
            __option.addEventListener('change', function(event){
                event.preventDefault();
                renderOption(__status.value, __option.value);
            })            
        }

        /**
         * =================================================================================================
         * |     @Re_Render | Re render all the necessary JS when clicked to switch/toggle theme           |
         * =================================================================================================
        */        

        // if( document.querySelector('.theme-toggle') != null ){
        //     document.querySelector('.theme-toggle').addEventListener('click', function() {

        //         getcorkThemeObject = localStorage.getItem("theme");
        //         getParseObject = JSON.parse(getcorkThemeObject)
        //         ParsedObject = getParseObject;

        //             /*
        //             ==================================
        //                 Bookings By Status Day | Options
        //             ==================================
        //             */

        //             bookingsStatusDay.updateOptions({
        //                 stroke: {
        //                     colors: ( ParsedObject.settings.layout.darkMode ? '#0e1726' : '#ffffff' )
        //                 },
        //                 plotOptions: {
        //                     pie: {
        //                         donut: {
        //                             labels: {
        //                                 value: {
        //                                     color: ( ParsedObject.settings.layout.darkMode ? '#bfc9d4' : '#0e1726' )
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }
        //             });

        //             /*
        //             ==================================
        //                 Bookings By Status Month | Options
        //             ==================================
        //             */

        //             bookingsStatusMonth.updateOptions({
        //                 stroke: {
        //                     colors: ( ParsedObject.settings.layout.darkMode ? '#0e1726' : '#ffffff' )
        //                 },
        //                 plotOptions: {
        //                     pie: {
        //                         donut: {
        //                             labels: {
        //                                 value: {
        //                                     color: ( ParsedObject.settings.layout.darkMode ? '#bfc9d4' : '#0e1726' )
        //                                 }
        //                             }
        //                         }
        //                     }
        //                 }
        //             });

        //             /*
        //                 =================================
        //                     Bookings Analytics Month | Options
        //                 =================================
        //             */
        //             bookingsAnalyticsMonth.updateOptions({
        //                 subtitle: {
        //                     style: {
        //                         color: ( ParsedObject.settings.layout.darkMode ? '#e0e6ed' : '#0e1726' )
        //                     }
        //                 },
        //                 title: {
        //                     style: {
        //                         color: ( ParsedObject.settings.layout.darkMode ? '#e0e6ed' : '#0e1726' )
        //                     },
        //                 },                    
        //             });

        //     });
        // }
    } catch(e) {
        console.log(e);
    }
})