
let cache = new Map();
let callcenter = {
    // fetchData: async function(url, containerId, params) {
    //     const container = document.getElementById(containerId);
    //     if (!container) return;
    
    //     // container.innerHTML = this.getLoader(); // Mostrar loader
    
    //     try {
    //         const response = await fetch(url, {
    //             method: 'POST',
    //             headers: {
    //                 'Content-Type': 'application/json',
    //                 'X-CSRF-TOKEN': csrfToken
    //             },
    //             body: JSON.stringify(params), // Convierte los datos a JSON
    //         });
    
    //         if (!response.ok) {
    //             throw new Error(`Error ${response.status}: ${response.statusText}`);
    //         }
    
    //         const data = await response.text();
    //         container.innerHTML = data;
    //     } catch (error) {
    //         console.error("Error en la petición:", error);
    //         container.innerHTML = '<p style="color:red;">Error al cargar datos.</p>';
    //     }
    // },

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
            return { data: { total_day: "N/A", total_month: "N/A" } }; // Devolver valores por defecto            
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

    //Está actualizando muchos elementos secuencialmente, lo que puede causar bloqueos en la interfaz.
    // reloadAll: async function(){
    //     // Elementos HTML
    //     const dailyGoal              = document.getElementById("dailyGoal");
    //     const progressDailyGoal      = document.getElementById("progressDailyGoal");
    //     const totalSales             = document.getElementById("totalSales");
    //     const totalServicesOperated  = document.getElementById("totalServicesOperated");
    //     const totalPendingServices   = document.getElementById("totalPendingServices");

    //     // Actualizar resumen general, al mostrar el loader ANTES de la solicitud
    //     dailyGoal.innerHTML              = this.getLoader();
    //     progressDailyGoal.innerHTML      = this.getLoader();
    //     totalSales.innerHTML             = this.getLoader();
    //     totalServicesOperated.innerHTML  = this.getLoader();
    //     totalPendingServices.innerHTML   = this.getLoader();
        
    //     try {
    //         // Data cargada
    //         const stats = await this.getStats();
    
    //         // Actualizar resumen general
    //         // USD (Dolares) → 'en-US', { style: 'currency', currency: 'USD' }
    //         // MXN (Pesos Mexicanos) → 'es-MX', { style: 'currency', currency: 'MXN' }
    //         // EUR (Euros) → 'de-DE', { style: 'currency', currency: 'EUR' }
    //         // CLP (Pesos Chilenos, sin decimales) → 'es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 }            
    //         dailyGoal.innerText = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(this.cleanNumber(stats.data.total_day));
    //         totalSales.innerText = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(this.cleanNumber(stats.data.total_month));
    //         totalServicesOperated.innerText = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(this.cleanNumber(stats.data.total_services_operated));
    //         totalPendingServices.innerText = new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(this.cleanNumber(stats.data.total_pending_services));

    //         let percentage = ( stats.data.percentage_daily_goal <= 50 ? 'danger' : ( stats.data.percentage_daily_goal <= 75 ? 'warning' : 'success' ) );
    //         progressDailyGoal.innerHTML = `<div class="progress">
    //                                 <div class="progress-bar bg-gradient-${percentage}" role="progressbar" style="width: ${stats.data.percentage_daily_goal}%" aria-valuenow="${stats.data.percentage_daily_goal}" aria-valuemin="0" aria-valuemax="100"></div>
    //                             </div>
    //                             <div class="">
    //                                 <div class="w-icon">
    //                                     <p>${stats.data.percentage_daily_goal}%</p>
    //                                 </div>
    //                             </div>`;

    //     } catch (error) {
    //         console.error("Error al obtener estadísticas:", error);
    //         dailyGoal.innerHTML             = '<p style="color:red;">Error al cargar.</p>';
    //         progressDailyGoal.innerHTML     = '<p style="color:red;">Error al cargar.</p>';
    //         totalSales.innerHTML            = '<p style="color:red;">Error al cargar.</p>';
    //         totalServicesOperated.innerHTML = '<p style="color:red;">Error al cargar.</p>';
    //         totalPendingServices.innerHTML  = '<p style="color:red;">Error al cargar.</p>';
    //     };
    // }

    // Ejecutar las actualizaciones de forma paralela con Promise.all:
    reloadAll: async function(){
        const elements = {
            dailyGoal: document.getElementById("dailyGoal"),
            progressDailyGoal: document.getElementById("progressDailyGoal"),
            totalSales: document.getElementById("totalSales"),
            totalServicesOperated: document.getElementById("totalServicesOperated"),
            totalPendingServices: document.getElementById("totalPendingServices")
        };
    
        Object.values(elements).forEach(el => el.innerHTML = this.getLoader());
    
        try {
            const stats = await this.getStats();
    
            await Promise.all([
                elements.dailyGoal.innerText = this.formatCurrency(stats.data.total_day),
                elements.totalSales.innerText = this.formatCurrency(stats.data.total_month),
                elements.totalServicesOperated.innerText = this.formatCurrency(stats.data.total_services_operated),
                elements.totalPendingServices.innerText = this.formatCurrency(stats.data.total_pending_services)
            ]);
    
            let percentage = (stats.data.percentage_daily_goal <= 50 ? 'danger' : 
                             (stats.data.percentage_daily_goal <= 75 ? 'warning' : 'success'));
    
            elements.progressDailyGoal.innerHTML = `<div class="progress">
                <div class="progress-bar bg-gradient-${percentage}" role="progressbar" 
                style="width: ${stats.data.percentage_daily_goal}%"></div>
            </div><div><p>${stats.data.percentage_daily_goal}%</p></div>`;
    
            document.querySelector(".btn-close").click(); // Simula el clic en el botón
        } catch (error) {
            console.error("Error al obtener estadísticas:", error);
            Object.values(elements).forEach(el => el.innerHTML = '<p style="color:red;">Error al cargar.</p>');
            document.querySelector(".btn-close").click(); // Simula el clic en el botón
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