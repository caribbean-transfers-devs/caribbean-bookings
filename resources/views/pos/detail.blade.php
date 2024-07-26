@php
    use App\Traits\RoleTrait;    
@endphp

@extends('layout.master')
@section('title') Detalle @endsection

@push('up-stack')    
    <link href="{{ mix('/assets/css/pos/detail.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/pos/detail.min.css') }}" rel="stylesheet" > 
@endpush

@push('bootom-stack')
    <script src="{{ mix('assets/js/views/pos/detail.min.js') }}"></script>
    <script src="{{ mix('assets/js/datatables.js') }}"></script>
@endpush

<script>
    var reservation_id = <?= $reservation->id ?>;
</script>
<script>
    var currency_exchange_data = <?= $currency_exchange_data ?>;
</script>

@section('content')
    <div class="container-fluid p-0">
        @csrf

        <div class="mb-3">
            <h1 class="h3 d-inline align-middle">Detalle de venta</h1>
        </div>

        <div class="row justify-content-center">

            <div class="col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ $reservation->site->name }}</h5>
                    </div>
                    <div class="card-body">
                        @php
                            // dump($reservation);
                        @endphp
                        <div class="button-list">
                            @if(RoleTrait::hasPermission(59))
                                <a href="#" class="btn btn-info change-date-btn" data-bs-toggle="modal" data-bs-target="#modify_pos_created_at">Cambiar fecha de creación</a>
                            @endif
                            @if(RoleTrait::hasPermission(61))
                                <a href="/reservations/detail/{{ $reservation->id }}" class="btn btn-primary">Actualización general</a>
                            @endif

                            @if( RoleTrait::hasPermission(77) && $reservation->is_complete == 0 && ( $reservation->site_id == 11 || $reservation->site_id == 21 ) )
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#completBookingModal">Completar reservación</button>
                            @endif                            
                        </div>

                        <table class="table table-sm mt-2 mb-4">
                            <tbody>
                                <tr>
                                    <th>Nombre</th>
                                    <td>{{ $reservation->client_first_name }} {{ $reservation->client_last_name }}</td>
                                </tr>
                                <tr>
                                    <th>E-mail</th>
                                    <td>{{ $reservation->client_email ? $reservation->client_email : 'No se registró el email' }}</td>
                                </tr>
                                <tr>
                                    <th>Teléfono</th>
                                    <td>{{ $reservation->client_phone ? $reservation->client_phone : 'No se registró el teléfono' }}</td>
                                </tr>
                                <tr>
                                    <th>Moneda</th>
                                    <td>{{ $reservation->currency }}</td>
                                </tr>                                
                                <tr>
                                    <th>Estatus</th>
                                    <td>
                                        @if ($data['status'] == "PENDING")
                                            <span class="badge bg-info">PENDING</span>
                                        @endif
                                        @if ($data['status'] == "CONFIRMED")
                                            <span class="badge bg-success">CONFIRMED</span>
                                        @endif
                                        @if ($data['status'] == "CANCELLED")
                                            <span class="badge bg-danger">CANCELLED</span>
                                        @endif                                                                             
                                    </td>
                                </tr>
                                <tr>
                                    <th>Unidad</th>
                                    <td>{{ $reservation->destination->name ?? '' }}</td>
                                </tr>
                                <tr>
                                    <th>Creación</th>
                                    <td>{{ $reservation->created_at }}</td>
                                </tr>
                                <tr>
                                    <th>Folio</th>
                                    <td>{{ $reservation->reference }}</td>
                                </tr>
                                <tr>
                                    <th>Vendedor</th>
                                    <td>{{ ( isset($reservation->vendor->name) ? $reservation->vendor->name : 'NO DEFINIDO' ) }}</td>
                                </tr>
                                <tr>
                                    <th>Capturista</th>
                                    <td>{{ ( isset($reservation->user->name) ? $reservation->user->name : 'NO DEFINIDO' ) }}</td>
                                </tr>
                                <tr>
                                    <th>Observaciones</th>
                                    <td>{{ $reservation->comments }}</td>
                                </tr>
                                <tr>
                                    <th>Origen</th>
                                    <td>{{ ($from_zone->name ?? '-') . ' | ' . $reservation->items[0]->from_name }}</td>
                                </tr>
                                <tr>
                                    <th>Destino</th>
                                    <td>{{ ($to_zone->name ?? '-') . ' | ' . $reservation->items[0]->to_name }}</td>
                                </tr>
                                <tr>
                                    <th>Total de venta</th>
                                    <td>${{ round($data['total_sales'], 2) }} {{ $reservation->currency }}</td>
                                </tr>
                                <tr>
                                    <th>Total pagado</th>
                                    <td>${{ round($data['total_payments'], 2) }} {{ $reservation->currency }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-striped table-sm mt-2 mb-4">
                            <thead>
                                <tr>
                                    <th>Método</th>
                                    <th>Descripción</th>
                                    <th>Referencia</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Moneda</th>
                                    <th class="text-center">TC</th>
                                    <th class="text-center">Tipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($reservation->payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>{{ $payment->description }}</td>
                                        <td>{{ $payment->reference ?? '-' }}</td>
                                        <td class="text-end">{{ number_format($payment->total) }}</td>
                                        <td class="text-center">{{ $payment->currency }}</td>
                                        <td class="text-end">{{ number_format($payment->exchange_rate) }}</td>
                                        <td class="text-end">{{ $payment->clip->name ?? 'Otro' }}</td>
                                    </tr>
                                @endforeach                                   
                            </tbody>
                        </table>

                        @if (RoleTrait::hasPermission(25))
                        <strong>Actividad</strong>

                        <ul class="timeline mt-2 mb-0">
                            @foreach ($reservation->followUps as $followUp)
                                <li class="timeline-item">
                                    <strong>[{{ $followUp->type }}]</strong>
                                    @php
                                        $time = $followUp->created_at->diffInMinutes(now());
                                        if($time > 90){
                                            $time /= 60;
                                            $time = number_format($time, 0, '.', '');
                                            $time .= ' hours';
                                        }else if($time > 1440){
                                            $time /= 1440;
                                            $time = number_format($time, 0, '.', '');
                                            $time .= ' days';
                                        }else{
                                            $time .= ' minutes';
                                        }
                                    @endphp
                                    <span class="float-end text-muted text-sm">{{ $time }} ago</span>
                                    <p>{{ $followUp->text }}</p>
                                </li>  
                            @endforeach                           
                        </ul>
                        @endif
                    </div>
                </div>
            </div>
            
        </div>        
        
    </div>

    <x-modals.modify_pos_created_at />
    @if( RoleTrait::hasPermission(77) && $reservation->is_complete == 0 && ( $reservation->site_id == 11 || $reservation->site_id == 21 ) )
        <x-modals.complet_post_booking :reservation="$reservation" :data="$data" :clips="$clips" :vendors="$vendors" :currencyexchangedata="$currency_exchange_data" />
        <x-modals.add_payment :clips="$clips" />
    @endif 

@endsection