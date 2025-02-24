
let cache = new Map();
let callcenter = {
    fetchData: async function(url, containerId, params) {
        const cacheKey = JSON.stringify({ url, params });

        const container = document.getElementById(containerId);
        if (!container) return;        
        
        if (cache.has(cacheKey)) {
            container.innerHTML = cache.get(cacheKey);
            return;
        }
    
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
            cache.set(cacheKey, data); // Guardar en cache
            container.innerHTML = data.trim();
        } catch (error) {
            console.error("Error en la petición:", error);
            container.innerHTML = '<p style="color:red;">Error al cargar datos.</p>'.trim();
        }
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

    getLoader: function() {
        return '<span class="container-loader"><i class="fa-solid fa-spinner fa-spin-pulse"></i></span>';
    },    

    cleanNumber: function(value) {
        if (!value) return 0;
        return Number(value.toString().replace(/[^0-9.-]+/g, ""));
    },

    formatCurrency: function(value) {
        return new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(this.cleanNumber(value));
    },

    // Ejecutar las actualizaciones de forma paralela con Promise.all:
    reloadAll: async function(){
        const elements = {
            dailyGoal: document.getElementById("dailyGoal"),
            progressDailyGoal: document.getElementById("progressDailyGoal"),
            totalSales: document.getElementById("totalSales"),
            totalServicesOperated: document.getElementById("totalServicesOperated"),
            totalPendingServices: document.getElementById("totalPendingServices"),
            totalCommissionOperated: document.getElementById("totalCommissionOperated"),
            totalCommissionOperated2: document.getElementById("totalCommissionOperated2"),
            totalCommissionPending: document.getElementById("totalCommissionPending"),
            listTargets: document.getElementById("listTargets"),
        };
    
        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());
    
        try {
            const stats = await this.getStats();
            console.log(stats);
    
            await Promise.all([
                elements.dailyGoal.innerText = this.formatCurrency(stats.data.daily_goal),
                elements.totalSales.innerText = this.formatCurrency(stats.data.total_month),
                elements.totalServicesOperated.innerText = this.formatCurrency(stats.data.total_services_operated),
                elements.totalPendingServices.innerText = this.formatCurrency(stats.data.total_pending_services),
                elements.totalCommissionOperated.innerText = this.formatCurrency(stats.data.total_commission_operated),
                elements.totalCommissionOperated2.innerText = this.formatCurrency(stats.data.total_commission_operated),
                elements.totalCommissionPending.innerText = this.formatCurrency(stats.data.total_commission_pending),
            ]);
    
            let percentage = (stats.data.percentage_daily_goal <= 50 ? 'danger' : 
                             (stats.data.percentage_daily_goal <= 75 ? 'warning' : 'success'));
    
            elements.progressDailyGoal.innerHTML = `<div class="progress">
                <div class="progress-bar bg-gradient-${percentage}" role="progressbar" 
                style="width: ${stats.data.percentage_daily_goal}%"></div>
            </div><div><p>${stats.data.percentage_daily_goal}%</p></div>`;

            let targets = '';
            if (stats.data.targets) {
                stats.data.targets.forEach(target => {
                    targets += `
                        <li class="list-group-item ">
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
}

document.addEventListener("DOMContentLoaded", function() {
    callcenter.reloadAll(); // Inicializar cargando todo

    // Configuraciones necesarias
    components.titleModal();
    components.calendarFilter();

    // Solictudes de filtros mediante el formulario
    if( document.getElementById('formSearch') ){
        document.getElementById('formSearch').addEventListener('submit', function(event) {
            event.preventDefault(); // Evita que el formulario se recargue
            callcenter.reloadAll();
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
            const data    = document.getElementById("data");

            // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
            data.innerHTML   = callcenter.getLoader().trim();

            // Definir parámetros de la petición
            const target = event.target;
            const _params = {
                type: target.dataset.type || "",
                date: document.getElementById('lookup_date').value || ""
            };
            
            $("#callcenterModal").modal('show');
            
            if (_params.type == "sales") callcenter.getSales(_params);
            if (_params.type == "completed" || _params.type == "pending") callcenter.getOperation(_params);
        }
    }, 300)); // 300ms de espera antes de ejecutar de nuevo
});