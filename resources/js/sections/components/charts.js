document.addEventListener("DOMContentLoaded", function () {
    //DECLARACIÓN DE CARIABLES
        //INPUTS DE DATA
        const __status = document.getElementById('bookingsStatus');
        const __payments = document.getElementById('dataMethodPayments');
        const __currencys = document.getElementById('dataCurrency');
        const __vehicles = document.getElementById('dataVehicles');
        const __services_type = document.getElementById('dataServiceTypeOperation');

        const __sites = document.getElementById('dataSites');
        const __destinations = document.getElementById('dataDestinations');
        const __drivers = document.getElementById('dataDriver');
        const __units = document.getElementById('dataUnit');
        const __origins = document.getElementById('dataOriginSale');        
        
        //EVENTOS DE LA GRAFICA
        const __container = document.querySelector('.box_container'); // Contenedor del scroll
        const __container_left = document.querySelector('.container_left'); // Contenedor del scroll
        const __container_right = document.querySelector('.container_right'); // Contenedor del scroll
        const __sections = document.querySelectorAll(".box_container > div");
        const __links = document.querySelectorAll('.box_sections > a');

        //RENDERIZADO DE GRAFICAS
        const __chartSaleStatus = document.getElementById('chartSaleStatus');
        const __chartSaleStatus2 = document.getElementById('chartSaleStatus2');
        const __chartSaleMethodPayments = document.getElementById('chartSaleMethodPayments');
        const __chartSaleMethodPayments2 = document.getElementById('chartSaleMethodPayments2');
        const __chartSaleCurrency = document.getElementById('chartSaleCurrency');
        const __chartSaleCurrency2 = document.getElementById('chartSaleCurrency2');
        const __chartSaleVehicle = document.getElementById('chartSaleVehicle');
        const __chartSaleVehicle2 = document.getElementById('chartSaleVehicle2');
        const __chartSaleServiceType = document.getElementById('chartSaleServiceType');
        const __chartSaleServiceType2 = document.getElementById('chartSaleServiceType2');

        const __chartSaleSites = document.getElementById('chartSaleSites');
        const __chartSaleDestination = document.getElementById('chartSaleDestination');
        const __chartSaleDrivers = document.getElementById('chartSaleDrivers');
        const __chartSaleUnits = document.getElementById('chartSaleUnits');
        const __chartSaleOriginSale = document.getElementById('chartSaleOriginSale');

    let charts = {
        dataStatus: __status?.value.trim() || "{}",
        dataMethodPayments: __payments?.value.trim() || "{}",
        dataCurrency: __currencys?.value.trim() || "{}",
        dataVehicles: __vehicles?.value.trim() || "{}",
        dataServiceType: __services_type?.value.trim() || "{}",

        dataSites: __sites?.value.trim() || "{}",                
        dataDestinations: __destinations?.value.trim() || "{}",
        dataDrivers: __drivers?.value.trim() || "{}",
        dataUnits: __units?.value.trim() || "{}",
        dataOriginSale: __origins?.value.trim() || "{}",
        settingsChart: function(_type, _option){
            let _data;
            switch (_option) {
                case 'paymentMethod':
                    _data = this.dataChartSaleMethodPayments();
                    break;
                case 'Currency':
                    _data = this.dataChartSaleCurrency();
                    break;
                case 'Vehicle':
                    _data = this.dataChartVehicle();
                    break;
                case 'serviceType':
                    _data = this.dataChartServiceType();
                    break
                default:
                    _data = this.dataChartSaleStatus();
                    break;
            };
            return {
                type: _type,
                data: {
                    labels: _data.map(row => {
                        const total = _data.reduce((sum, item) => sum + item.counter, 0);
                        const percentage = ((row.counter / total) * 100).toFixed(2) + '%';
                        return `${row.name} (${percentage})`; // Agrega el porcentaje en la leyenda
                    }),
                    datasets: [
                        {
                            data: _data.map(row => row.counter),
                            borderWidth: 0, // Hace las líneas del gráfico más delgadas
                            cutout: '70%' // Reduce el grosor del doughnut
                        }
                    ]
                },
                options: {
                    responsive: true, // Hacer el gráfico responsivo
                    maintainAspectRatio: false, // Permitir que el gráfico ajuste su altura además de su ancho
                    plugins: {
                        legend: {
                            display: true,  // Mostrar las etiquetas
                            position: 'bottom', // Colocar las etiquetas debajo del gráfico
                            labels: {
                                padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                font: {
                                    size: 12, // Tamaño de la fuente de los labels
                                    color: '#000' // Cambia el color de los labels a negro
                                },
                                color: '#000' // Asegura que el color de los labels sea negro
                            }
                        },
                        datalabels: {
                            display: false // Oculta los datalabels en el gráfico
                        }
                    }
                },
                plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
            }
        },
        dataChartSaleStatus: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataStatus));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartSaleStatus: function(){
            if( __chartSaleStatus != null ){
                new Chart(__chartSaleStatus, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartSaleStatus().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartSaleStatus().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
            if( __chartSaleStatus2 != null ){
                new Chart(__chartSaleStatus2, this.settingsChart('doughnut','Status'));
            }
        },
        dataChartSaleMethodPayments: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataMethodPayments));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartSaleMethodPayments: function(){
            if( __chartSaleMethodPayments != null ){
                new Chart(__chartSaleMethodPayments, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartSaleMethodPayments().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartSaleMethodPayments().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
            if( __chartSaleMethodPayments2 != null ){
                new Chart(__chartSaleMethodPayments2, this.settingsChart('doughnut','paymentMethod'));
            }
        },
        dataChartSaleCurrency: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataCurrency));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartSaleCurrency: function(){
            if( __chartSaleCurrency != null ){
                new Chart(__chartSaleCurrency, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartSaleCurrency().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartSaleCurrency().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
            if( __chartSaleCurrency2 != null ){
                new Chart(__chartSaleCurrency2, this.settingsChart('doughnut','Currency'));
            }
        },
        dataChartVehicle: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataVehicles));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartVehicle: function(){
            if( __chartSaleVehicle != null ){
                new Chart(__chartSaleVehicle, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartVehicle().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartVehicle().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
            if( __chartSaleVehicle2 != null ){
                new Chart(__chartSaleVehicle2, this.settingsChart('doughnut','Vehicle'));
            }
        },
        dataChartServiceType: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataServiceType));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartServiceType: function(){            
            if( __chartSaleServiceType != null ){
                new Chart(__chartSaleServiceType, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartServiceType().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartServiceType().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
            if( __chartSaleServiceType2 != null ){
                new Chart(__chartSaleServiceType2, this.settingsChart('doughnut','serviceType'));
            }
        },
        dataChartSaleSites: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataSites));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartSaleSites: function(){
            if( __chartSaleSites != null ){
                new Chart(__chartSaleSites, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartSaleSites().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartSaleSites().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
        },
        dataChartSaleDestinations: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataDestinations));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartSaleDestinations: function(){
            if( __chartSaleDestination != null ){
                new Chart(__chartSaleDestination, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartSaleDestinations().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartSaleDestinations().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
        },
        dataChartDrivers: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataDrivers));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartDrivers: function(){
            if( __chartSaleDrivers != null ){
                new Chart(__chartSaleDrivers, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartDrivers().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartDrivers().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
        },
        dataChartUnits: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataUnits));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartUnits: function(){
            if( __chartSaleUnits != null ){
                new Chart(__chartSaleUnits, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartUnits().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartUnits().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
        },        
        dataChartSaleOriginSale: function(){
            let object = [];
            const systems = Object.entries(JSON.parse(this.dataOriginSale));
            systems.forEach( ([key, data]) => {
                object.push(data);
            });
            return object;
        },
        renderChartSaleOriginSale: function(){
            if( __chartSaleOriginSale != null ){
                new Chart(__chartSaleOriginSale, {
                    type: 'pie',
                    data: {
                        labels: charts.dataChartSaleOriginSale().map(row => row.name),
                        datasets: [
                            {
                                data: charts.dataChartSaleOriginSale().map(row => row.counter),
                            }
                        ]
                    },
                    options: {
                        responsive: true, // Hacer el gráfico responsivo
                        maintainAspectRatio: true, // Permitir que el gráfico ajuste su altura además de su ancho
                        plugins: {
                            legend: {
                                display: true,  // Mostrar las etiquetas
                                position: 'right', // Colocar las etiquetas debajo del gráfico
                                labels: {
                                    padding: 5, // Ajustar el espacio entre la leyenda y el gráfico
                                    boxWidth: 20, // Tamaño de los cuadros de color de la leyenda
                                    font: {
                                        size: 12, // Tamaño de la fuente de los labels
                                        color: '#000' // Cambia el color de los labels a negro
                                    },
                                    color: '#000' // Asegura que el color de los labels sea negro
                                }
                            },
                            datalabels: {
                                display: true,
                                formatter: (value, context) => {                                                                                
                                    const total = context.chart._metasets[0].total;
                                    const percentage = ((value / total) * 100).toFixed(2) + '%';
                                    return percentage; // Mostrar porcentaje en el gráfico
                                },
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                },
                                anchor: 'end',
                                align: 'start'
                            }
                        }
                    },
                    plugins: [ChartDataLabels] // Asegúrate de incluir el plugin ChartDataLabels
                });
            }
        },        
        resetClassSection: function(){
            if( __links.length > 0 ){
                __links.forEach(__link => {
                    __link.classList.remove('active');
                });
            }
        },
    };

    // Agregamos un evento de scroll para el manejo de la activación
    // Función para manejar el scroll
    if( __container != null ){
        __container.addEventListener('scroll', () => {
            let activeSectionIndex = -1; // Índice inicial
            let activeSectionId = null; // ID inicial 

            // Recorremos las secciones para verificar cuál está visible en el viewport
            __sections.forEach((__section, __key) => {
                const containerTop = __container.scrollTop; // Posición actual del scroll
                const sectionTop = __section.offsetTop - __container.offsetTop; // Posición de la sección dentro del contenedor
                const sectionHeight = __section.offsetHeight;

                // Si el centro de la sección está visible dentro del área del contenedor
                const isVisible = 
                    containerTop >= sectionTop - sectionHeight / 2 &&
                    containerTop < sectionTop + sectionHeight / 2;
                
                if (isVisible) {
                    activeSectionIndex = __key; // Guardar índice
                    activeSectionId = __section.id; // Guardar ID
                }
            });

            // Mostrar el índice y el ID de la sección activa
            if (activeSectionId) {
                console.log(`Sección activa: ID=${activeSectionId}, Índice=${activeSectionIndex}`);
            }

            // Activamos el enlace correspondiente en el menú
            __links.forEach((__link) => {
                __link.classList.remove("active");
                if (__link.getAttribute("href") === `#${activeSectionId}`) {
                    __link.classList.add("active");
                }
            });
        });
    }

    // Opcional: Smooth scrolling al hacer clic en los enlaces
    if( __links.length > 0 ){
        __links.forEach(__link => {
            __link.addEventListener('click', function(event){
                event.preventDefault();
                charts.resetClassSection();
                const targetId = this.getAttribute('href').substring(1); // Obtén el ID del destino
                const targetElement = document.getElementById(targetId);            
                this.classList.add('active');
                if (targetElement) {
                    // Desplázate suavemente hacia el elemento
                    targetElement.scrollIntoView({
                      behavior: 'smooth', // Animación suave
                      block: 'start' // Alinea el inicio del elemento con la parte superior de la vista
                    });
                }            
            })
        });
    }

    //Validamos que existan los DOM, donde renderizara las graficas
    charts.renderChartSaleStatus();
    charts.renderChartSaleMethodPayments();
    charts.renderChartSaleCurrency();
    charts.renderChartVehicle();
    charts.renderChartServiceType();

    charts.renderChartSaleSites();    
    charts.renderChartSaleDestinations();
    charts.renderChartDrivers();
    charts.renderChartUnits();
    charts.renderChartSaleOriginSale();
});
