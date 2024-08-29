@extends('layout.app')
@section('title') Admin Dashboard @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/dashboard2.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/sections/dashboard2.min.css') }}" rel="stylesheet">    
    <style>
.overlayDashboard {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease;
  z-index: 1035;
}

.popover {
  position: absolute;
  top: 50px;
  left: 50%;
  transform: translateX(0) scale(0.8);
  width: 500px; /* Ajusta el ancho según necesites */
  padding: 15px;
  background-color: #f5f5f5; /* Color de fondo gris claro */
  color: #333;
  border-radius: 8px;
  box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1); /* Pequeño efecto de sombra */
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
  z-index: 9999; /* Mayor que el overlay */
}

.popover-content h3 {
  margin-top: 0;
  font-size: 14px;
  color: #333;
}

.popover-content p {
  font-size: 14px;
  color: #333;
}

.close-btn {
  background: none;
  border: none;
  color: #999;
  font-size: 20px;
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}

.close-btn:hover {
  color: #666;
}

.popover.show {
  opacity: 1;
  visibility: visible;
  transform: translateX(0) scale(1);
}

.overlayDashboard.show {
  opacity: 1;
  visibility: visible;
}

.__open_popover{
    cursor: pointer;
}
    </style>
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="{{ mix('/assets/js/sections/dashboard2.min.js') }}"></script>
    <script>
        let dashboard = {
            dataSystems: @json(( isset($bookingsData['data']) ? $bookingsData['data'] : [] )),
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
            dataChartSale: function(){
                let object = [];
                const systems = Object.entries(this.dataSystems);
                systems.forEach( ([key, data]) => {
                    // console.log(key);
                    // console.log(data);
                    object.push(data);
                });
                return object;
            },
            renderChartSale: function(){
                // Calcular el total de 'counter'
                const totalCount = dashboard.dataChartSale().reduce((sum, system) => sum + system['accumulated'].counter, 0);
                // Calcular el porcentaje de cada 'counter'
                const percentages = dashboard.dataChartSale().map(site => ((site['accumulated'].counter / totalCount) * 100).toFixed(2) + '%');

                if( document.getElementById('chartSale') != null ){
                    new Chart(document.getElementById('chartSale'), {
                        type: 'pie',
                        data: {
                            labels: dashboard.dataChartSale().map(row => row.name),
                            // labels: dashboard.dataChartSale().map((row, index) => `${row.name} (${percentages[index]})`),
                            datasets: [
                                {
                                    data: dashboard.dataChartSale().map(row => row['accumulated'].counter),
                                    backgroundColor: dashboard.dataChartSale().map(row => row.background)
                                }
                            ]
                        },
                        options: {
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        title: function(tooltipItems) {
                                            // Mostrar el nombre del sitio
                                            return tooltipItems[0].label;
                                        },
                                        label: function(tooltipItem) {
                                            console.log(tooltipItem);                                            
                                            const index = tooltipItem.dataIndex;
                                            const site = dashboard.dataChartSale()[index];
                                            // Mostrar el monto en pesos y dólares junto con el porcentaje
                                            return [
                                                // `${site.name}:`,
                                                // `Porcentaje: ${percentages[index]}`,
                                                // `Pesos: $${site.amount.toLocaleString()} MXN`,
                                                // `Dólares: $${site.amount2.toLocaleString()} USD`
                                                `TOTAL DE VENTA: $ ${site['accumulated'].total.toLocaleString()}`,
                                                `TOTAL DE VENTA EN USD: $ ${site['accumulated']['USD'].total.toLocaleString()}`,
                                                `TOTAL DE VENTA EN MXN: $ ${site['accumulated']['MXN'].total.toLocaleString()}`,
                                            ];
                                        }
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
            }
        };
    
        dashboard.renderChartSale();

        const overlay = document.getElementById("overlayDashboard");
        document.querySelectorAll('.__open_popover').forEach(function(element) {
            element.addEventListener('click', function(event) {
                var popover = document.getElementById('popover');
                // Posiciona el popover a la derecha del icono
                var rect = event.target.getBoundingClientRect();
                let bodyPopoper = popover.querySelector('.body');
                console.log(bodyPopoper);
                
                let content = '';
                let info = event.target.dataset.info;
                console.log(info);
                
                let infoJSON = JSON.parse(info);
                console.log(infoJSON);
                
                if( infoJSON.hasOwnProperty('accumulated') ){
                    content += `
                            <div class='d-flex align-items-center justify-content-between'>
                                <div class='d-flex w-50 cell_head'>TOTAL</div> 
                                <div class='d-flex w-50 cell_head'>$ ${dashboard.number_format(infoJSON.accumulated.total, 2, '.', ',')}</div>
                            </div>
                            <div class='d-flex flex-wrap align-items-center justify-content-between'>
                                <div class='d-flex w-50 cell_head'>PESOS</div>     
                                <div class='d-flex w-50 cell_head'>DOLARES</div>
                                <div class='d-flex w-50 '>$ ${dashboard.number_format(infoJSON.accumulated.MXN.total, 2, '.', ',')}</div>
                                <div class='d-flex w-50 '>$ ${dashboard.number_format(infoJSON.accumulated.USD.total, 2, '.', ',')}</div>
                            </div>
                    `;
                }
                if( infoJSON.hasOwnProperty('confirmed') ){
                    content += `
                            <div class='d-flex align-items-center justify-content-between'>
                                <div class='d-flex w-50 cell_head confirmed'>CONFIRMADAS Y PENDIENTES</div> 
                                <div class='d-flex w-50 cell_head confirmed height'>$ ${dashboard.number_format(infoJSON.confirmed.total, 2, '.', ',')}</div>
                            </div>
                            <div class='d-flex flex-wrap align-items-center justify-content-between'>
                                <div class='d-flex w-50 cell_head'>PESOS</div>     
                                <div class='d-flex w-50 cell_head'>DOLARES</div>
                                <div class='d-flex w-50 '>$ ${dashboard.number_format(infoJSON.confirmed.MXN.total, 2, '.', ',')}</div>
                                <div class='d-flex w-50 '>$ ${dashboard.number_format(infoJSON.confirmed.USD.total, 2, '.', ',')}</div>
                            </div>
                    `;
                }
                if( infoJSON.hasOwnProperty('cancelled') ){
                    content += `
                            <div class='d-flex align-items-center justify-content-between'>
                                <div class='d-flex w-50 cell_head cancelled'>CANCELADAS</div> 
                                <div class='d-flex w-50 cell_head cancelled'>$ ${dashboard.number_format(infoJSON.cancelled.total, 2, '.', ',')}</div>
                            </div>
                            <div class='d-flex flex-wrap align-items-center justify-content-between'>
                                <div class='d-flex w-50 cell_head'>PESOS</div>     
                                <div class='d-flex w-50 cell_head'>DOLARES</div>
                                <div class='d-flex w-50 '>$ ${dashboard.number_format(infoJSON.cancelled.MXN.total, 2, '.', ',')}</div>
                                <div class='d-flex w-50 '>$ ${dashboard.number_format(infoJSON.cancelled.USD.total, 2, '.', ',')}</div>
                            </div>
                    `;
                }                
                bodyPopoper.innerHTML = content;
                
                popover.style.top = `${rect.top + rect.height / 2 - popover.offsetHeight / 2 + window.scrollY}px`;
                popover.style.left = `${rect.right + 20}px`;

                popover.classList.toggle("show");
                overlay.classList.toggle("show");
            });
        });

        document.getElementById("closePopover").addEventListener("click", function() {
            var popover = document.getElementById('popover');
            popover.classList.remove("show");
            overlay.classList.remove("show");
        });

        // Ocultar el popover cuando se hace clic en otro lugar
        document.addEventListener('click', function(event) {
            var popover = document.getElementById('popover');
            var target = event.target;

            // Verificar si el clic fue fuera del popover o del icono
            if (!popover.contains(target) && !target.classList.contains('__open_popover')) {
                popover.classList.remove("show");
                overlay.classList.remove("show");
            }
        });
    </script>
@endpush

@section('content')


<div id="overlayDashboard" class="overlayDashboard"></div>
<div id="popover" class="popover" data-bs-placement="right">
  <div class="popover-arrow"></div>
  <div class="popover-content">
    <h3>Información detallada</h3>    
    <button id="closePopover" class="close-btn">&times;</button>
    <div class="body"></div>
  </div>
</div>

    <div class="row layout-top-spacing">
        {{-- @dump($bookingsData); --}}
        <div class="statbox widget box box-shadow">
            <form action="" method="POST" class="row mb-3" id="formFilter">
                <div class="col-12 col-sm-4 mb-3 mb-lg-0">
                    <label class="form-label" for="lookup_date">Fecha de creación</label>
                    <input type="text" name="date" id="lookup_date" class="form-control" value="{{ $data['init'] }} - {{ $data['end'] }}">
                </div>
                <div class="col-12 col-sm-2 align-self-end">
                    <button type="submit" class="btn btn-primary btn-lg btn-filter w-100 _effect--ripple waves-effect waves-light">Filtrar</button>
                </div>
            </form>
            
            <div class="report_sale">
                <div>
                    <table class="table table-bordered">
                        <thead class="head_platform">
                            <th></th>
                            <th>TOTAL DE VENTAS</th>
                            <th>TOTAL DE SERVICIOS</th>
                            <th>TOTAL DE VENTAS (CONFIRMADAS Y PENDIENTES)</th>
                            <th>TOTAL DE SERVICIOS (CONFIRMADOS Y PENDEINTES)</th>
                            <th>TOTAL DE VENTAS CANCELADAS</th>
                            <th>TOTAL DE SERVICIOS CANCELADOS</th>
                        </thead>
                        <tbody>
                            <tr style="background: #bfc9d4;color: #f8538d;">
                                <td></td>
                                <td>$ {{ number_format($bookingsData['accumulated']['total'],2) }}</td>
                                <td>{{ $bookingsData['accumulated']['counter'] }}</td>
                                <td>$ {{ number_format($bookingsData['confirmed']['total'],2) }}</td>
                                <td>{{ $bookingsData['confirmed']['counter'] }}</td>
                                <td>$ {{ number_format($bookingsData['cancelled']['total'],2) }}</td>
                                <td>{{ $bookingsData['cancelled']['counter'] }}</td>
                            </tr>                            
                            @foreach ($bookingsData['data'] as $keyData => $data)
                                <tr style="background: {{ $data['background'] }};color: {{ $data['color'] }};">
                                    <td>{{ $data['name'] }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle __open_popover position-relative" data-info='{{ json_encode($data) }}'><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></td>
                                    <td>$ {{ number_format($data['accumulated']['total'],2) }}</td>
                                    <td>{{ $data['accumulated']['counter'] }}</td>
                                    <td>$ {{ number_format($data['confirmed']['total'],2) }}</td>
                                    <td>{{ $data['confirmed']['counter'] }}</td>
                                    <td>$ {{ number_format($data['cancelled']['total'],2) }}</td>
                                    <td>{{ $data['cancelled']['counter'] }}</td>
                                </tr>
                                @foreach ($data['items'] as $keyitem => $item)
                                <tr>
                                    <td>{{ $item['name'] }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-help-circle __open_popover position-relative" data-info='{{ json_encode($item) }}'><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg></td>
                                    <td>$ {{ number_format($item['accumulated']['total'],2) }}</td>
                                    <td>{{ $item['accumulated']['counter'] }}</td>
                                    <td>$ {{ number_format($item['confirmed']['total'],2) }}</td>
                                    <td>{{ $item['confirmed']['counter'] }}</td>
                                    <td>$ {{ number_format($item['cancelled']['total'],2) }}</td>
                                    <td>{{ $item['cancelled']['counter'] }}</td>
                                </tr>                
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <canvas class="p-3" id="chartSale" width="400" height="400"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection