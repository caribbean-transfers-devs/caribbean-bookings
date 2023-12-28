@extends('layout.master')
@section('title') Admin Dashboard @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/dashboards/admin.min.css') }}" rel="preload" as="style">
    <link href="{{ mix('/assets/css/dashboards/admin.min.css') }}" rel="stylesheet"> 
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('bootom-stack')
    <script>
        var datos = @json($items);
        
        function getDays(objeto) {
            var dates = [];
            
            for (var new_date in objeto) {                
                if (objeto.hasOwnProperty(new_date) && /\d{4}-\d{2}-\d{2}/.test(new_date)) {                    
                    var update_day = parseInt(new_date.split('-')[2]);
                    dates.push(update_day);
                }
            }

            return dates;
        }

        function getCounter(objeto) {
            var counterByDate = [];

            for (var new_date in objeto) {                
                if (objeto.hasOwnProperty(new_date) && /\d{4}-\d{2}-\d{2}/.test(new_date)) {                    
                    var counter = objeto[new_date].counter;
                    counterByDate.push(counter);
                }
            }

            return counterByDate;
        }


        const ctx = document.getElementById('myChart');
    
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: getDays(datos),
                datasets: [{
                label: 'Resumen del mes',
                data: getCounter(datos),
                borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
  </script>
@endpush

@section('content')
    <div class="container-fluid p-0">
        <h1 class="h3 mb-3 button_">Reservaciones</h1>
        <div class="row">
            <div class="col-12 col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Resumen por día</h4>
                    </div>
                    <table class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Día</th>
                                <th class="text-center">#</th>
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $day_data = [
                                    "total" => 0,
                                    "USD" => 0,
                                    "MXN" => 0
                                ];
                            @endphp
                            @foreach($items as $key => $value)
                                @php
                                    $day_data['total'] += $value['counter'];
                                    $day_data['USD'] += $value['USD'];
                                    $day_data['MXN'] += $value['MXN'];
                                @endphp
                                <tr>
                                    <td>{{ $key }}</td>
                                    <td class="text-center">{{ $value['counter'] }}</td>
                                    <td class="text-end">{{ number_format($value['USD'],2) }}</td>
                                    <td class="text-end">{{ number_format($value['MXN']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" class="text-end">{{ $day_data['total'] }}</td>                                
                                <td class="text-end">{{ number_format($day_data['USD'],2) }}</td>
                                <td class="text-end">{{ number_format($day_data['MXN'],2) }}</td>
                            <tr>
                        </tfoot>
                    </table>
                </div>                
            </div>
            <div class="col-12 col-sm-8">
                <canvas id="myChart"></canvas>
            </div>
        </div>

    </div>
@endsection