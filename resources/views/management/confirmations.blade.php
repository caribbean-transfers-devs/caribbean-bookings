@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;
@endphp
@extends('layout.app')
@section('title') Confirmaciónes @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management_confirmation.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management_confirmation.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>    
    <script src="{{ mix('assets/js/sections/operations/confirmation.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtros de confirmaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            )            
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12 layout-spacing">
            <div class="widget-content widget-content-area br-8">
                @if ($errors->any())
                    <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <table id="dataConfirmations" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ESTATUS DE CONFIRMACIÓN</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">PICKUP</th>
                            <th class="text-center">TIPO</th>
                            <th class="text-center">ESTATUS DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">CLIENTE</th>
                            <th class="text-center">VEHÍCULO</th>
                            <th class="text-center">PASAJEROS</th>
                            <th class="text-center">DESDE</th>
                            <th class="text-center">HACIA</th>
                            <th class="text-center">PAGO</th>
                            <th class="text-center">TOTAL</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">COMISIÓNABLE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($confirmations)>=1)
                            @foreach($confirmations as $key => $confirmation)                         
                                <tr>
                                    <td class="text-center">
                                        @if (RoleTrait::hasPermission(40))
                                            <?=OperationTrait::renderStatusConfirmation($confirmation)?>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $confirmation->site_name }}</td>
                                    <td class="text-center">{{ OperationTrait::setDateTime($confirmation, "time") }}</td>
                                    <td class="text-center">{{ $confirmation->final_service_type }}</td>
                                    <td class="text-center"><?=OperationTrait::renderServiceStatus($confirmation)?></td>
                                    <td class="text-center">
                                        @if (RoleTrait::hasPermission(38))
                                            <a href="/reservations/detail/{{ $confirmation->reservation_id }}"><p class="mb-1">{{ $confirmation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $confirmation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $confirmation->full_name }}</td>
                                    <td class="text-center">{{ $confirmation->service_type_name }}</td>
                                    <td class="text-center">{{ $confirmation->passengers }}</td>
                                    <td class="text-center">{{ OperationTrait::setFrom($confirmation, "name") }} {{ $confirmation->operation_type == 'arrival' && !empty($confirmation->flight_number) ? ' ('.$confirmation->flight_number.')' : '' }}</td>
                                    <td class="text-center">{{ OperationTrait::setTo($confirmation, "name") }}</td>
                                    <td class="text-center" <?=BookingTrait::classStatusPayment($confirmation)?>>{{ BookingTrait::statusPayment($confirmation->payment_status) }}</td>
                                    <td class="text-center">{{ number_format($confirmation->total_balance,2) }}</td>
                                    <td class="text-center">{{ $confirmation->currency }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-{{ $confirmation->is_commissionable == 1 ? 'success' : 'danger' }}" type="button">{{ $confirmation->is_commissionable == 1 ? "SI" : "NO" }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-modals.filters.bookings :data="$data" />
@endsection