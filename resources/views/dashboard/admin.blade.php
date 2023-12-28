@extends('layout.master')
@section('title') Admin Dashboard @endsection

@push('up-stack')
    <link href="{{ mix('/assets/css/dashboards/admin.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/dashboards/admin.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
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

            </div>
        </div>

    </div>
@endsection