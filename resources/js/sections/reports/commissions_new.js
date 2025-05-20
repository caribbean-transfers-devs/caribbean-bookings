let commissions = {
    selectedUsers: function(){
        let usersElement = document.getElementById('user');
        return usersElement ? this.selectedOptions(usersElement) : [];         
    },
    selectedStatus: function(){
        let statusElement = document.getElementById('status');
        return statusElement ? this.selectedOptions(statusElement) : [];       
    },
    GetDate: function(ParamDate = null){
        let _filter_date = ParamDate || document.getElementById('filter_date').value || "";
    
        if (!_filter_date) {
            return ""; // Si no hay fecha, retorna una cadena vacía
        }
        // console.log("Fecha recibida:", _filter_date);

        // Aseguramos que el formato sea YYYY-MM-DD y extraemos el año y el mes
        let [year, month] = _filter_date.split("-").map(Number);

        // Calcular primer y último día del mes seleccionado
        let start = new Date(Date.UTC(year, month - 1, 1)); // Mes en base 0
        let end = new Date(Date.UTC(year, month, 0)); // Último día del mes

        let startFormatted = start.toISOString().split("T")[0]; // YYYY-MM-DD
        let endFormatted = end.toISOString().split("T")[0]; // YYYY-MM-DD
    
        return `${startFormatted} a ${endFormatted}`;
    },
    selectedOptions: function(_this){
        return Array.from(_this.selectedOptions).map(option => option.value);
    },
    optionsSettings: {
        chart: {
          fontFamily: 'Poppins, serif',
          height: 410,
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
        dataLabels: {
          enabled: false
        },
        markers: {
            size: 5,
            hover: {
                size: 7
            }
        },        
        stroke: {
            show: true,
            curve: 'smooth',
            width: 2,
            lineCap: 'square'
        },
        xaxis: {
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
            offsetY: 2,
            style: {
                fontSize: '11px',
                fontFamily: 'Poppins, serif',
                cssClass: 'apexcharts-xaxis-title',
            },
          }
        },
        yaxis: {
          labels: {
            formatter: function(value, index) {
              return (value / 1000) + ' K'
            },
            offsetX: -15,
            offsetY: 0,
            style: {
                fontSize: '11px',
                fontFamily: 'Poppins, serif',
                cssClass: 'apexcharts-yaxis-title',
            },
          }
        },
        tooltip: {
            marker: {
              show: false,
            },
            y: {
                formatter: function (value, { seriesIndex, dataPointIndex, w }) {
                    return `$${value.toFixed(2)}`;
                }
            }
        },        
        responsive: [{
          breakpoint: 575,
          options: {
            legend: {
                offsetY: -50,
            },
          },
        }]
    },
   
    getStats: async function() {
        try {
            const _params    = {
                date: this.GetDate(),
                user: this.selectedUsers(),
                status: this.selectedStatus()
            };
            console.log(_params);
            

            const http = await fetch('/reports/stats/commissions/get', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(_params),
            });
    
            if (!http.ok) {
                throw new Error(`Error ${http.status}: ${http.statusText}`);
            }
    
            return await http.json();
        } catch (error) {
            console.error("Error en getStats:", error);
            return { data: { daily_goal: "N/A", total_day: "N/A", percentage_daily_goal: "N/A", total_month: "N/A", total_services_operated: "N/A", total_pending_services: "N/A" } }; // Devolver valores por defecto            
        }
    },
    fetchData: async function(url) {
        try {
            const _params    = {
                date: this.GetDate(),
                user: this.selectedUsers(),
                status: this.selectedStatus()
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(_params),
            });
    
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
    
            return await response.text();
        } catch (error) {
            console.error("Error en fetchData:", error);
            return { data: { daily_goal: "N/A", total_day: "N/A", percentage_daily_goal: "N/A", total_month: "N/A", total_services_operated: "N/A", total_pending_services: "N/A" } }; // Devolver valores por defecto
        }
    },    
    fetchDataCharts: async function(url) {
        try {
            const _params    = {
                date: this.GetDate(),
                user: this.selectedUsers(),
                status: this.selectedStatus()
            };

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(_params),
            });
    
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
    
            return await response.json();
        } catch (error) {
            console.error("Error en fetchDataCharts:", error);
            return { data: { daily_goal: "N/A", total_day: "N/A", percentage_daily_goal: "N/A", total_month: "N/A", total_services_operated: "N/A", total_pending_services: "N/A" } }; // Devolver valores por defecto
        }
    },

    cleanNumber: function(value) {
        if (!value) return 0;
        return Number(value.toString().replace(/[^0-9.-]+/g, ""));
    },
    formatCurrency: function(value, asNumber = false) {
        let cleanedValue = this.cleanNumber(value);
        return asNumber ? cleanedValue.toFixed(2) : 
            new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(cleanedValue);
    },
    getLoader: function() {
        return '<span class="container-loader"><i class="fa-solid fa-spinner fa-spin-pulse"></i></span>';
    },

    // Ejecutar las actualizaciones de forma paralela con Promise.all:
    reloadAll: async function(){
        const elements = {
            exchangeInfo: document.getElementById("exchangeInfo"),
            totalSales: document.getElementById("totalSales"),
            totalOperationSales: document.getElementById("totalOperationSales"),
            totalCommissions: document.getElementById("totalCommissions"),
        };
    
        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());
    
        try {
            const stats = await this.getStats();
    
            await Promise.all([
                elements.exchangeInfo.innerText = this.formatCurrency(stats.data.EXCHANGE_COMMISSION),
                elements.totalSales.innerText = this.formatCurrency(stats.data.TOTAL_SALES),
                elements.totalOperationSales.innerText = this.formatCurrency(stats.data.TOTAL_OPERATIONS),
                elements.totalCommissions.innerText = this.formatCurrency(stats.data.TOTAL_COMMISSIONS),
            ]);
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
        }
    },    

    //SELLER SALES
    seriesSellerSales: function(response, option = "one") {
        let data = response.data;
        let series = {};
        let seriesTotal = {};
        let seriesQuantity = {};
        let labels = [];
        let formattedSeries = [];
        let chartData = {};

        Object.keys(data).forEach((date) => {
            let dayInfo = data[date];
            labels.push(dayInfo.DATE); // Agregamos la fecha al eje X

            Object.keys(dayInfo.DATA).forEach((sellerId) => {
                let seller = dayInfo.DATA[sellerId];
                let sellerName = seller.NAME;
                if (!series[sellerName]) {
                    series[sellerName] = {
                        name: sellerName,
                        data: []
                    };
                }                
                if( option == "one" ){
                    series[sellerName].data.push(this.formatCurrency(seller.TOTAL, true)); // Agregamos las ventas del día
                }else{  
                    // Agregamos un objeto con el TOTAL y QUANTITY en la misma posición
                    series[sellerName].data.push({
                        x: dayInfo.DATE, 
                        y: seller.TOTAL,
                        quantity: seller.QUANTITY
                    });
                }
            });
        });

        // Convertimos el objeto series en un array
        formattedSeries = Object.values(series);

        // Construimos el objeto final para ApexCharts
        chartData.series = formattedSeries;        
        chartData.labels = labels;

        return chartData;
    },
    chartSellerSales: function(response, option = "one"){
        let data = this.seriesSellerSales(response, option);
        if( option == "two" ){
            data.tooltip = {
                shared: true,
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    let seriesName = w.config.series[seriesIndex].name;
                    let date = w.globals.categoryLabels[dataPointIndex]; // Recupera la fecha
                    let total = w.config.series[seriesIndex].data[dataPointIndex].y;
                    let quantity = w.config.series[seriesIndex].data[dataPointIndex].quantity;
                    return `<div class="apexcharts-tooltip-title" style="font-family: Poppins, serif; font-size: 12px;">${date}</div> <!-- Título restaurado -->
                            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: flex-start; min-width: 200px;">
                                <div class="apexcharts-tooltip-text" style="font-family: Poppins, serif; font-size: 12px;">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">${seriesName}</span>
                                    </div>                                
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total: </span>
                                        <span class="apexcharts-tooltip-text-y-value">$${total.toFixed(2)}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Ventas: </span>
                                        <span class="apexcharts-tooltip-text-y-value">${quantity}</span>
                                    </div>                                    
                                </div>
                            </div>`;
                }
            };
        }
        const object = Object.assign({}, this.optionsSettings, data);
        var chart1 = new ApexCharts(document.getElementById("revenueMonthly"),object).render();
    },
    reloadSalesChartsSellers: async function(){
        const elements = {
            titleCharts: document.getElementById("titleCharts"),
            revenueMonthly: document.getElementById("revenueMonthly"),
        };

        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());

        try {
            const chartsOperations = await this.fetchDataCharts('/reports/sales/stats/charts/commissions');

            await Promise.all([
                elements.titleCharts.innerText = "Estadistica de ventas por agente de call center periodo del: " + chartsOperations.date,
            ]);

            revenueMonthly.innerHTML = "";
            this.chartSellerSales(chartsOperations, 'two');
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
        }        
    },

    //SALES
    seriesSales: function(response, option = "one") {
        let chartData = { series:[ { name: 'VENTAS', data: [] } ], labels: [] };
        Object.entries(response.data).forEach( ([date, dataDay]) => {
            chartData.labels.push((dataDay.DATE));
            let sales = chartData.series.find(serie => serie.name === 'VENTAS');
            if( option == "one" ){
                sales.data.push(this.formatCurrency(dataDay.TOTAL, true)); // Agregar un valor
            }else{  
                // Agregamos un objeto con el TOTAL y QUANTITY en la misma posición
                sales.data.push({
                    x: dataDay.DATE,
                    y: dataDay.TOTAL,
                    quantity: dataDay.QUANTITY
                });
            }
        });
        return chartData;
    },
    chartSales: function(response, option = "one"){
        let data = this.seriesSales(response, option);
        data.colors = ['#00ab55'];
        if( option == "two" ){
            data.tooltip = {
                shared: true,
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    let seriesName = w.config.series[seriesIndex].name;
                    let date = w.globals.categoryLabels[dataPointIndex]; // Recupera la fecha
                    let total = w.config.series[seriesIndex].data[dataPointIndex].y;
                    let quantity = w.config.series[seriesIndex].data[dataPointIndex].quantity;
                    return `<div class="apexcharts-tooltip-title" style="font-family: Poppins, serif; font-size: 12px;">${date}</div> <!-- Título restaurado -->
                            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: flex-start; min-width: 200px;">
                                <div class="apexcharts-tooltip-text" style="font-family: Poppins, serif; font-size: 12px;">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total: </span>
                                        <span class="apexcharts-tooltip-text-y-value">$${total.toFixed(2)}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Ventas: </span>
                                        <span class="apexcharts-tooltip-text-y-value">${quantity}</span>
                                    </div>                                    
                                </div>
                            </div>`;
                }
            };
        }
        const object = Object.assign({}, this.optionsSettings, data);      
        var chart1 = new ApexCharts(document.getElementById("revenueMonthly"),object).render();
    },
    reloadSalesCharts: async function(){
        const elements = {
            titleCharts: document.getElementById("titleCharts"),
            revenueMonthly: document.getElementById("revenueMonthly"),
        };

        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());

        try {
            const chartsOperations = await this.fetchDataCharts('/reports/sales/stats/charts/commissions');

            await Promise.all([
                elements.titleCharts.innerText = "Estadistica de ventas periodo del: " + chartsOperations.date,
            ]);

            revenueMonthly.innerHTML = "";
            this.chartSales(chartsOperations, 'two');
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
        }        
    },

    //SELLER OPERATIONS
    seriesOperationSales: function(response, option = "one") {
        let data = response.data;
        let series = {};
        let labels = [];
        let formattedSeries = [];
        let chartData = {};

        Object.keys(data).forEach((date) => {
            let dayInfo = data[date];
            labels.push(dayInfo.DATE); // Agregamos la fecha al eje X

            Object.keys(dayInfo.DATA).forEach((sellerId) => {
                let seller = dayInfo.DATA[sellerId];
                let sellerName = seller.NAME;
                if (!series[sellerName]) {
                    series[sellerName] = {
                        name: sellerName,
                        data: []
                    };
                }                
                if( option == "one" ){
                    series[sellerName].data.push(this.formatCurrency(seller.TOTAL, true)); // Agregamos las ventas del día
                }else{  
                    // Agregamos un objeto con el TOTAL y QUANTITY en la misma posición
                    series[sellerName].data.push({
                        x: dayInfo.DATE, 
                        y: seller.TOTAL,
                        quantity: seller.QUANTITY
                    });
                }
            });
        });

        // Convertimos el objeto series en un array
        formattedSeries = Object.values(series);

        // Construimos el objeto final para ApexCharts
        chartData.series = formattedSeries;        
        chartData.labels = labels;

        return chartData;
    },
    chartOperationSales: function(response, option = "one"){
        let data = this.seriesOperationSales(response, option);
        if( option == "two" ){
            data.tooltip = {
                shared: true,
                custom: function ({ series, seriesIndex, dataPointIndex, w }) {
                    let seriesName = w.config.series[seriesIndex].name;
                    let date = w.globals.categoryLabels[dataPointIndex]; // Recupera la fecha
                    let total = w.config.series[seriesIndex].data[dataPointIndex].y;
                    let quantity = w.config.series[seriesIndex].data[dataPointIndex].quantity;
                    return `<div class="apexcharts-tooltip-title" style="font-family: Poppins, serif; font-size: 12px;">${date}</div> <!-- Título restaurado -->
                            <div class="apexcharts-tooltip-series-group apexcharts-active" style="order: 1; display: flex; align-items: flex-start; min-width: 200px;">
                                <div class="apexcharts-tooltip-text" style="font-family: Poppins, serif; font-size: 12px;">
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">${seriesName}</span>
                                    </div>                                
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Total: </span>
                                        <span class="apexcharts-tooltip-text-y-value">$${total.toFixed(2)}</span>
                                    </div>
                                    <div class="apexcharts-tooltip-y-group">
                                        <span class="apexcharts-tooltip-text-y-label">Ventas: </span>
                                        <span class="apexcharts-tooltip-text-y-value">${quantity}</span>
                                    </div>                                    
                                </div>
                            </div>`;
                }
            };
        }
        const object = Object.assign({}, this.optionsSettings, data);
        var chart1 = new ApexCharts(document.getElementById("revenueMonthly"),object).render();
    },
    reloadSalesOperationCharts: async function(){
        const elements = {
            titleCharts: document.getElementById("titleCharts"),
            revenueMonthly: document.getElementById("revenueMonthly"),
        };

        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());

        try {
            const chartsOperations = await this.fetchDataCharts('/reports/operations/stats/charts/commissions');
            console.log(chartsOperations);
            
            await Promise.all([
                elements.titleCharts.innerText = "Estadistica de operación por agente de call center periodo del: " + chartsOperations.date,
            ]);

            revenueMonthly.innerHTML = "";
            this.chartOperationSales(chartsOperations, 'two');
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
        }        
    },    
};

const __filter_date = document.getElementById('filter_date');
document.addEventListener("DOMContentLoaded", function() {    
    // Configuraciones necesarias
    commissions.reloadAll(); // Inicializar cargando las estadisticas
    commissions.reloadSalesCharts();

    if( __filter_date ){
        let calendarInstance = filters.calendarFilter(__filter_date, {
            mode: "single",
            minDate: null,
            plugins: [
                new monthSelectPlugin({
                  shorthand: true, //defaults to false
                //   dateFormat: "m.y", //defaults to "F Y"
                  dateFormat: "Y-m-d", //defaults to "F Y"
                  altFormat: "F Y", //defaults to "F Y"
                })
            ],
        });

        // También puedes usarlo externamente
        calendarInstance.set("onChange", function(selectedDates, dateStr, instance) {
            commissions.GetDate(dateStr);
            commissions.reloadAll();
            commissions.reloadSalesCharts();
        });
    }

    if( document.getElementById('user') ){
        document.getElementById('user').addEventListener('change', function() {
            let selectedValues = commissions.selectedStatus(); // Obtener valores seleccionados
            commissions.reloadAll(); // Inicializar cargando las estadisticas
            commissions.reloadSalesCharts();
        });
    }

    if( document.getElementById('status') ){
        document.getElementById('status').addEventListener('change', function() {
            let selectedValues = commissions.selectedStatus(); // Obtener valores seleccionados
            commissions.reloadAll(); // Inicializar cargando las estadisticas
            commissions.reloadSalesCharts();
        });
    }

    document.addEventListener("click", components.debounce(async function (event) {
        if (event.target.classList.contains('getData')) {
            event.preventDefault();
            
            // Obtener datos del elemento clickeado
            const { type } = event.target.dataset;
            
            $("#callcenterModal").modal('show');
            
            // Elementos HTML
            const elements = {
                title: document.getElementById("callcenterModalLabel"),
                container: document.getElementById("data"),
            };

            // Validar que los elementos existen antes de usarlos
            if (!elements.title || !elements.container) {
                console.error("Error: No se encontraron los elementos del DOM necesarios.");
                return;
            }

            // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
            Object.values(elements).forEach(el => el.innerHTML = commissions.getLoader().trim());

            // Determinar la URL según el tipo de reporte
            let title;
            let url;
            switch (type) {
                case "sales":
                    title = "Listado de reservas vendidas";
                    url = "/reports/sales/data/commissions/get";
                    break;
                case "operations":
                    title = "Listado de servicios operados";
                    url = "/reports/operations/data/commissions/get";
                    break;
                default:
                    title = "Listado de comisiones";
                    url = "/reports/commissions/data/commissions/get";
                    break;
            }            

            try {
                const data = await commissions.fetchData(url);                
                
                // Validar que los elementos existen antes de modificar el contenido
                await Promise.all([
                    elements.title.innerHTML = title.trim(),
                    elements.container.innerHTML = data.trim(),
                ]);

                components.actionTableChart($('.table-chart-general'), 'commissions');
            } catch (error) {
                console.error("Error al obtener datos:", error);
                Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar datos.</p>');
            }
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo
});