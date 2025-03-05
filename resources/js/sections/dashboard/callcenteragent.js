
let cache = new Map();
let callcenter = {
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
        responsive: [{
          breakpoint: 575,
          options: {
            legend: {
                offsetY: -50,
            },
          },
        }]
    },
    fetchData: async function(url, containerId, params) {
        // const cacheKey = JSON.stringify({ url, params });

        const container = document.getElementById(containerId);
        if (!container) return;        
        
        // if (cache.has(cacheKey)) {
        //     container.innerHTML = cache.get(cacheKey);
        //     return;
        // }
    
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(params),
            });
    
            if (!response.ok) {
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }
    
            const data = await response.text();
            // cache.set(cacheKey, data); // Guardar en cache
            container.innerHTML = data.trim();
        } catch (error) {
            console.error("Error en la petición:", error);
            container.innerHTML = '<p style="color:red;">Error al cargar datos.</p>'.trim();
        }
    },

    fetchDataCharts: async function(url) {
        try {
            const _params    = {
                date: document.getElementById('lookup_date').value || ""
            };

            const http = await fetch(url, {
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

    getSales: function(_params) {
        console.log(_params);
        this.fetchData('/callcenters/sales/get', "data", _params);
    },
    
    getOperation: function(_params) {
        this.fetchData('/callcenters/operations/get', "data", _params);
    },

    getStats: async function() {
        try {
            const http = await fetch('/callcenters/stats/get', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ date: document.getElementById('lookup_date').value }),
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

    seriesSales: function(data) {
      let object = { series:[ { name: 'VENTAS', data: [] } ], labels: [] };
      const sales = Object.entries(data.data);
      sales.forEach( ([date, dataDay]) => {
          let sales = object.series.find(serie => serie.name === 'VENTAS');
          // Formatear el total como moneda con 2 decimales
          let Total = this.formatCurrency(dataDay.TOTAL, true);
          sales.data.push(Total); // Agregar un valor
          object.labels.push((dataDay.DATE));
      });
      return object;
    },
    chartSales: function(response){
      let data = this.seriesSales(response);
      let options = {
        colors: ['#00ab55'],
        series: data.series,
        labels: data.labels
      };
      const object = Object.assign({}, options, this.optionsSettings);
      var chart1 = new ApexCharts(document.getElementById("revenueMonthly"),object).render();
    },

    seriesOperations: function(data) {
        let object = { series:[ { name: 'COMPLETADAS', data: [] }, { name: 'PENDIENTES', data: [] } ], labels: [] };
        const operations = Object.entries(data.data);
        operations.forEach( ([date, dataDay]) => {
            let completed = object.series.find(serie => serie.name === 'COMPLETADAS');
            let pending = object.series.find(serie => serie.name === 'PENDIENTES');            
            // Formatear el total como moneda con 2 decimales
            let TotalCompleted = this.formatCurrency(dataDay.COMPLETED.TOTAL, true);
            let TotalPending = this.formatCurrency(dataDay.PENDING.TOTAL, true);    
            completed.data.push(TotalCompleted); // Agregar un valor
            pending.data.push(TotalPending); // Agregar un valor
            object.labels.push((dataDay.DATE));
        });
        return object;
    },
    chartOperations: function(response){
        let data = this.seriesOperations(response);
        let options = {
          colors: ['#00ab55', '#e2a03f'],
          series: data.series,
          labels: data.labels
        };
        const object = Object.assign({}, options, this.optionsSettings);
        var chart1 = new ApexCharts(document.getElementById("revenueMonthly"),object).render();
    },

    // Ejecutar las actualizaciones de forma paralela con Promise.all:
    reloadAll: async function(){
        const elements = {
            dateInfo: document.getElementById("dateInfo"),
            dateQuotationInfo: document.getElementById("dateQuotationInfo"),            
            exchangeInfo: document.getElementById("exchangeInfo"),
            dailyGoal: document.getElementById("dailyGoal"),
            progressDailyGoal: document.getElementById("progressDailyGoal"),
            totalSales: document.getElementById("totalSales"),
            totalServicesOperated: document.getElementById("totalServicesOperated"),
            totalPendingServices: document.getElementById("totalPendingServices"),
            totalCommissionOperated: document.getElementById("totalCommissionOperated"),
            // totalCommissionOperated2: document.getElementById("totalCommissionOperated2"),
            // totalCommissionPending: document.getElementById("totalCommissionPending"),
            listBreakdownCommissions: document.getElementById("listBreakdownCommissions"),
            listTargets: document.getElementById("listTargets"),            
        };
    
        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());
    
        try {
            const stats = await this.getStats();            
    
            await Promise.all([
                elements.dateInfo.innerText = document.getElementById('lookup_date').value,
                elements.dateQuotationInfo.innerText = document.getElementById('lookup_date').value,
                elements.exchangeInfo.innerText = this.formatCurrency(stats.data.exchange),
                elements.dailyGoal.innerText = this.formatCurrency(stats.data.daily_goal),
                elements.totalSales.innerText = this.formatCurrency(stats.data.total_month),
                elements.totalServicesOperated.innerText = this.formatCurrency(stats.data.total_services_operated),
                elements.totalPendingServices.innerText = this.formatCurrency(stats.data.total_pending_services),
                elements.totalCommissionOperated.innerText = this.formatCurrency(stats.data.total_commission_operated),
                // elements.totalCommissionOperated2.innerText = this.formatCurrency(stats.data.total_commission_operated),
                // elements.totalCommissionPending.innerText = this.formatCurrency(stats.data.total_commission_pending),
            ]);
    
            let percentage = (stats.data.percentage_daily_goal <= 50 ? 'danger' : 
                             (stats.data.percentage_daily_goal <= 75 ? 'warning' : 'success'));
    
            elements.progressDailyGoal.innerHTML = `<div class="progress">
                <div class="progress-bar bg-gradient-${percentage}" role="progressbar" 
                style="width: ${stats.data.percentage_daily_goal}%"></div>
            </div><div><p>${stats.data.percentage_daily_goal}%</p></div>`;

            listBreakdownCommissions.innerHTML = `
                                                    <li class="list-group-item">
                                                        <div class="media-body">
                                                            <h6 class="tx-inverse text-uppercase">Total operado</h6>
                                                            <p class="amount">${this.formatCurrency(stats.data.total_services_operated_month)}</p>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item ">
                                                        <div class="media-body">
                                                            <h6 class="tx-inverse text-uppercase">Descuento por inversion</h6>
                                                            <p class="mg-b-0">Porcentage de descuento: <strong>${stats.data.percentage_commission_investment}%</strong></p>
                                                            <p class="amount">${this.formatCurrency(stats.data.total_investment_discount_operated)}</p>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item ">
                                                        <div class="media-body">
                                                            <h6 class="tx-inverse text-uppercase">Total menos decuento</h6>                                                            
                                                            <p class="amount">${this.formatCurrency(stats.data.total_services_operated_investment_discount)}</p>
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item ">
                                                        <div class="media-body">
                                                            <h6 class="tx-inverse text-uppercase">Total de comisión</h6>
                                                            <p class="mg-b-0">Porcentage de comisión: <strong>${stats.data.percentage_commission}%</strong></p>
                                                            <p class="amount">${this.formatCurrency(stats.data.total_commission_operated)}</p>
                                                        </div>
                                                    </li>
                                                 `;

            let targets = '';
            if (stats.data.targets) {
                console.log(stats.data.targets);                
                stats.data.targets.forEach(target => {
                    targets += `
                        <li class="list-group-item ${ target.status ? 'active' : '' }">
                            <div class="media-body">
                                <h6 class="tx-inverse text-uppercase">${target.name}</h6>
                                <p class="mg-b-0">Porcentage de comisión: <strong>${target.percentage}%</strong></p>
                                <p class="amount">${this.formatCurrency(target.amount)}</p>
                            </div>
                        </li>
                    `;
                });
            }
            elements.listTargets.innerHTML = targets;

            document.querySelector(".close-filters").click(); // Simula el clic en el botón
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
            document.querySelector(".close-filters").click(); // Simula el clic en el botón
        }
    },

    reloadChartsSales: async function(){
        const elements = {
            titleCharts: document.getElementById("titleCharts"),
            revenueMonthly: document.getElementById("revenueMonthly"),                       
        };

        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());

        try {
            const chartsOperations = await this.fetchDataCharts('/callcenters/stats/charts/sales');            

            await Promise.all([
                elements.titleCharts.innerText = "Estadistica de ventas periodo del: " + chartsOperations.date,
            ]);

            revenueMonthly.innerHTML = "";
            this.chartSales(chartsOperations);
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
        }        
    },

    reloadChartsOperations: async function(){
        const elements = {
            titleCharts: document.getElementById("titleCharts"),
            revenueMonthly: document.getElementById("revenueMonthly"),
        };

        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());

        try {
            const chartsOperations = await this.fetchDataCharts('/callcenters/stats/charts/opertions');

            await Promise.all([
                elements.titleCharts.innerText = "Estadistica de servicios operados y pendientes de operar periodo del: " + chartsOperations.date,
            ]);

            revenueMonthly.innerHTML = "";
            this.chartOperations(chartsOperations);
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
        }        
    }
}

document.addEventListener("DOMContentLoaded", function() {
    callcenter.reloadAll(); // Inicializar cargando las estadisticas
    callcenter.reloadChartsSales();

    // Configuraciones necesarias
    components.titleModal();
    components.calendarFilter();

    // Solictudes de filtros mediante el formulario
    if( document.getElementById('formSearch') ){
        document.getElementById('formSearch').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita que el formulario se recargue
            callcenter.reloadAll();
            callcenter.reloadChartsSales();
        });
    }

    // Problema: Falta de debounce en eventos de click
    // Si un usuario hace clic rápidamente en elementos con getData, se pueden disparar muchas solicitudes.
    // Usar debounce para evitar múltiples ejecuciones innecesarias:
    function debounce(func, delay) {
        let timer;
        return function(...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    document.addEventListener("click", debounce(function (event) {
        if (event.target.classList.contains('getData')) {
            event.preventDefault();

            // Elementos HTML
            const data       = document.getElementById("data");

            // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
            data.innerHTML   = callcenter.getLoader().trim();

            // Definir parámetros de la petición
            const target     = event.target;
            const _params    = {
                type: target.dataset.type || "",
                date: document.getElementById('lookup_date').value || ""
            };
            
            $("#callcenterModal").modal('show');
            
            const titleDashboard = document.getElementById('callcenterModalLabel');
            if (_params.type == "sales"){
                titleDashboard.innerText = "Listado de reservas vendidas";
                callcenter.getSales(_params);
            }
            if (_params.type == "completed" || _params.type == "pending"){
                titleDashboard.innerText = ( _params.type == "completed" ? "Listado de servicios operados" : "Listado de servicios pendientes de operar" );
                callcenter.getOperation(_params);
            } 
        }

        if (event.target.classList.contains('wallet-text')) {
            event.preventDefault();
            
            const containerBreakdown = document.getElementById('containerBreakdownCommissions');
            containerBreakdown.classList.toggle('d-none');
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo    
});