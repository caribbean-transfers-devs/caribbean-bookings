@extends('layout.master')
@section('title') Reservaciones @endsection

@push('up-stack')
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

@push('bootom-stack')
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
    <script src="{{ mix('assets/js/views/reservationsIndex.js') }}"></script>    
@endpush

@section('content')
    <div class="container-fluid p-0">

        <h1 class="h3 mb-3">Reservaciones</h1>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Reservaciones</h5>
                        <div class="card-header d-flex justify-content-end align-items-center gap-3"> 
                            <div class="input-group">
                                <input type="text" name="lookup_date" id="lookup_date" class="form-control" value="{{ $from }} a {{ $to }}">
                                <button class="btn btn-success" type="button" onclick="Search()">Ver</button>
                            </div>                                                                           
                        </div>                       
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mt-3">
                            <table id="reservations_table" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo</th>
                                        <th>Telefono</th>
                                        <th>Fecha Pickup</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($reservations as $reservation)
                                        <tr>
                                            <td>{{ $reservation->client_first_name }} {{ $reservation->client_last_name }}</td>
                                            <td>{{ $reservation->client_email }}</td> 
                                            <td>{{ $reservation->client_phone }}</td> 
                                            <td>
                                                @foreach ($reservation->items as $item)
                                                    @foreach ($item->services as $service)
                                                        {{ $service->pickup }} 
                                                    @endforeach  
                                                @endforeach                                               
                                            </td>                                             
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="actions" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Acciones
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="actions">
                                                        <li><a class="dropdown-item" href="reservations/detail/{{ $reservation->id }}">Ver Detalle</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>                                        
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection