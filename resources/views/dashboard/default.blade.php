@extends('layout.master')
@section('title') Dashboard @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
    <script>
        $(function() {
            $('#reservations_table').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
                }
            });
        });
    </script>
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Dashboard Reservaciones</h1>

        <div class="row">
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Reservaciones este mes</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="calendar"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $general_services }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Reservaciones mes pasado</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="calendar"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $last_month_general_services }}</h1>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col mt-0">
                                <h5 class="card-title">Reservaciones Hoy</h5>
                            </div>

                            <div class="col-auto">
                                <div class="stat text-primary">
                                    <i class="align-middle" data-feather="calendar"></i>
                                </div>
                            </div>
                        </div>
                        <h1 class="mt-1 mb-3">{{ $one_services_today->count() + $two_services_today->count() }}</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Servicios de Hoy</h5>
                    </div>
                    <div class="card-body">
                        <table id="reservations_table" class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Estatus</th>
                                    <th>Cliente</th>
                                    <th>Servicio</th>
                                    <th>Pasajeros</th>                                    
                                    <th>Destino</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($one_services_today as $item)                                        
                                    <tr>
                                        <td>
                                            <a href="reservations/detail/{{ $item->id }}"> {{ $item->code }}</a>
                                        </td> 
                                        <td class="text-center">
                                            @switch($item->op_one_status)
                                                @case('CONFIRMED')
                                                    <span class="badge bg-success">Confirmado</span>
                                                @break
                                                @case('PENDING')
                                                    <span class="badge bg-info">Pendiente</span>
                                                @break
                                                @case('CANCELLED')
                                                    <span class="badge bg-danger">Cancelado</span>
                                                @break
                                                @case('NOSHOW')
                                                    <span class="badge bg-warning">No show</span>
                                                @break
                                                @default                                                            
                                            @endswitch
                                        </td> 
                                        <td>{{ $item->reservations->client_first_name }} {{ $item->reservations->client_last_name }}</td>                                           
                                        <td>{{ $item->destination_service->name }}</td>
                                        <td class="text-center">{{ $item->passengers }}</td>
                                        <td class="text-center">{{ $item->destination->name }}</td>
                                    </tr>        
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection