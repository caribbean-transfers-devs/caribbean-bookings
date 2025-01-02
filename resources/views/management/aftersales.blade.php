@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;
@endphp
@extends('layout.app')
@section('title') POST Venta @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/management_aftersales.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/management_aftersales.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/operations/spam.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array();
        // if (RoleTrait::hasPermission(41)){
        //     array_push($buttons,
        //         array(
        //             'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar',
        //             'className' => 'btn btn-primary __btn_create',
        //             'attr' => array(
        //                 'data-title' =>  "Filtros de spam",
        //                 'data-bs-toggle' => 'modal',
        //                 'data-bs-target' => '#filterModal'
        //             )
        //         )
        //     );

        //     array_push($buttons,
        //         array(
        //             'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar reporte de excel',
        //             'className' => 'btn btn-primary',
        //             'extend' => 'excelHtml5',
        //         )
        //     );
        // }
        // if (RoleTrait::hasPermission(70)){
        //     array_push($buttons,
        //         array(
        //             'text' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="cloud-download" class=""><path fill="" fill-rule="evenodd" d="M12 4a7 7 0 00-6.965 6.299c-.918.436-1.701 1.177-2.21 1.95A5 5 0 007 20a1 1 0 100-2 3 3 0 01-2.505-4.65c.43-.653 1.122-1.206 1.772-1.386A1 1 0 007 11a5 5 0 0110 0 1 1 0 00.737.965c.646.176 1.322.716 1.76 1.37a3 3 0 01-.508 3.911 3.08 3.08 0 01-1.997.754 1 1 0 00.016 2 5.08 5.08 0 003.306-1.256 5 5 0 00.846-6.517c-.51-.765-1.28-1.5-2.195-1.931A7 7 0 0012 4zm1 7a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L13 16.586V11z" clip-rule="evenodd"></path></svg> Exportar teléfonia de Box in plan',
        //             'className' => 'btn btn-primary __btn_export',
        //             'attr' => array(
        //                 'data-title' =>  "Generar reporte de excel",
        //                 'data-bs-toggle' => 'modal',
        //                 'data-bs-target' => '#filterModalExport'
        //             )
        //         )
        //     );
        // }
    @endphp

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

    <div class="layout-top-spacing widget-content widget-content-area br-8 mb-3 p-2">
        <button class="btn btn-primary _btn_create" data-title="Filtros de spam" data-bs-toggle="modal" data-bs-target="#filterModal"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtrar</button>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <ul class="nav nav-pills" id="animateLine" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="animated-underline-pending-tab" data-bs-toggle="tab" href="#animated-underline-pending" role="tab" aria-controls="animated-underline-pending" aria-selected="false" tabindex="-1"> Reservas pendientes</button>
                </li>                
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="animated-underline-spam-tab" data-bs-toggle="tab" href="#animated-underline-spam" role="tab" aria-controls="animated-underline-spam" aria-selected="false" tabindex="-1"> Spam</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content" id="animateLineContent-4">
        <div class="tab-pane fade show active" id="animated-underline-pending" role="tabpanel" aria-labelledby="animated-underline-badge-pending">                    
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="row info p-0">
                            <table id="dataBookings" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                                <thead>
                                    <tr>
                                        <th class="text-center">ID</th>
                                        <th class="text-center">CÓDIGO</th>
                                        <th class="text-center">REFERENCIA</th>
                                        <th class="text-center">FECHA</th>
                                        <th class="text-center">HORA</th>
                                        <th class="text-center">SITIO</th>
                                        <th class="text-center">ORIGEN DE VENTA</th>
                                        <th class="text-center">ESTATUS</th>
                                        <th class="text-center">CLIENTE</th>
                                        <th class="text-center">VEHÍCULO</th>
                                        <th class="text-center">PAX</th>
                                        <th class="text-center">DESDE</th>                            
                                        <th class="text-center">HACIA</th>
                                        <th class="text-center">ESTATUS DE PAGO</th>
                                        <th class="text-center">TOTAL DE RESERVACIÓN</th>
                                        <th class="text-center">BALANCE</th>
                                        <th class="text-center">MONEDA</th>
                                        <th class="text-center">MÉTODO DE PAGO</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($bookings) >= 1)
                                        @foreach ($bookings as $item)
                                            <tr class="{{ ( $item->is_today != 0 ? 'bs-tooltip' : '' ) }}" title="{{ ( $item->is_today != 0 ? 'Es una reserva que se opera el mismo día en que se creo #: '.$item->reservation_id : '' ) }}" style="{{ ( $item->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}" data-reservation="{{ $item->reservation_id }}" data-is_round_trip="{{ $item->is_round_trip }}">
                                                <td class="text-center">{{ $item->reservation_id }}</td>
                                                <td class="text-center">
                                                    @php
                                                        $codes_string = "";
                                                        $codes = explode(",",$item->reservation_codes);
                                                        foreach ($codes as $key => $code) {
                                                            $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                                        }
                                                    @endphp
                                                    @if (RoleTrait::hasPermission(38))
                                                        <a href="/reservations/detail/{{ $item->reservation_id }}"><?=$codes_string?></a>
                                                    @else
                                                        <?=$codes_string?>
                                                    @endif
                                                </td>
                                                <td class="text-center"><?=( !empty($item->reference) ? '<p class="mb-1">'.$item->reference.'</p>' : '' )?></td>
                                                <td class="text-center">{{ date("Y-m-d", strtotime($item->created_at)) }}</td>
                                                <td class="text-center">{{ date("H:i", strtotime($item->created_at)) }}</td>
                                                <td class="text-center">{{ $item->site_name }}</td>
                                                <td class="text-center">{{ !empty($item->origin_code) ? $item->origin_code : 'PAGINA WEB' }}</td>
                                                <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($item->reservation_status) }}">{{ BookingTrait::statusBooking($item->reservation_status) }}</button></td>
                                                <td class="text-center">{{ $item->full_name }}</td>
                                                <td class="text-center">{{ $item->service_type_name }}</td>
                                                <td class="text-center">{{ $item->passengers }}</td>                                    
                                                <td class="text-center">{{ $item->from_name }}</td>
                                                <td class="text-center">{{ $item->to_name }}</td>
                                                <td class="text-center" <?=BookingTrait::classStatusPayment($item)?>>{{ BookingTrait::statusPayment($item->payment_status) }}</td>
                                                <td class="text-center" <?=BookingTrait::classStatusPayment($item)?>>{{ number_format(($item->total_sales),2) }}</td>
                                                <td class="text-center" {{ (($item->total_balance > 0)? "style=background-color:green;color:white;font-weight:bold;":"") }}>{{ number_format($item->total_balance,2) }}</td>                                
                                                <td class="text-center">{{ $item->currency }}</td>
                                                <td class="text-center">{{ $item->payment_type_name }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info __payment_info bs-tooltip" title="Ver informacón detallada de los pagos" data-reservation="{{ $item->reservation_id }}"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>                            
                        </div>
                    </div>
                </div>
            </div>                        
        </div>
        <div class="tab-pane fade" id="animated-underline-spam" role="tabpanel" aria-labelledby="animated-underline-badge-spam">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                    <div class="section general-info">
                        <div class="row info p-0">
                            <table id="dataSpams" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                                <thead>
                                    <tr>
                                        <th class="text-center">ESTATUS DE SPAM</th>
                                        <th class="text-center">CODE</th>
                                        <th class="text-center"># LLAMADAS ACEPTADAS</th>
                                        <th class="text-center">SITIO</th>
                                        <th class="text-center">PICKUP</th>
                                        <th class="text-center">TIPO</th>
                                        <th class="text-center">ROUND TRIP</th>
                                        <th class="text-center">OPERACIÓN</th>
                                        <th class="text-center">CÓDIGO</th>
                                        <th class="text-center">CLIENTE</th>
                                        <th class="text-center">TELÉFONO</th>
                                        <th class="text-center">CORREO</th>
                                        <th class="text-center">VEHÍCULO</th>
                                        <th class="text-center">PASAJEROS</th>
                                        <th class="text-center">DESDE</th>
                                        <th class="text-center">HACIA</th>
                                        <th class="text-center">TOTAL</th>
                                        <th class="text-center">MONEDA</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(sizeof($spams)>=1)
                                        @foreach($spams as $keyS => $spam)
                                            @if( in_array($spam->final_service_type, ["ARRIVAL", "TRANSFER"]) )
                                                @php
                                                    switch ($spam->spam) {
                                                        case 'PENDING':
                                                            $spam_status = 'btn-secondary';
                                                            break;
                                                        case 'SENT':
                                                            $spam_status = 'btn-info';
                                                            break;
                                                        case 'LATER':
                                                            $spam_status = 'btn-warning';
                                                            break;
                                                        case 'CONFIRMED':
                                                            $spam_status = 'btn-success';
                                                            break;
                                                        case 'ACCEPT':
                                                            $spam = 'btn-success';
                                                            break;                                                
                                                        case 'REJECTED':
                                                            $spam_status = 'btn-danger';
                                                            break;
                                                        default:
                                                            $spam_status = 'btn-secondary';
                                                            break;
                                                    }        
                                                @endphp
                                                <tr>
                                                    <td class="text-center">
                                                        <div class="btn-group" role="group">
                                                            <button id="actions" type="button" class="btn {{ $spam_status }} dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-id="{{$spam->id}}">
                                                                {{ $spam->spam }}
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                                            </button>                                                
                                                            <div class="dropdown-menu" aria-labelledby="actions">
                                                                <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$spam->id}},'PENDING','btn-secondary')"><i class="flaticon-home-fill-1 mr-1"></i> PENDING</a>
                                                                <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$spam->id}},'SENT','btn-info')"><i class="flaticon-home-fill-1 mr-1"></i> SENT</a>
                                                                <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$spam->id}},'LATER','btn-warning')"><i class="flaticon-home-fill-1 mr-1"></i> LATER</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$spam->id}},'CONFIRMED','btn-success')"><i class="flaticon-home-fill-1 mr-1"></i> CONFIRMED</a>
                                                                <a href="javascript:void(0);" class="dropdown-item" onClick="updateSpam(event,{{$spam->id}},'REJECTED','btn-danger')"><i class="flaticon-home-fill-1 mr-1"></i> REJECTED</a>
                                                            </div>
                                                        </div>                                 
                                                    </td>
                                                    <td class="text-center">{{ $spam->id }}</td>
                                                    <td class="text-center">{{ $spam->spam_count }}</td>                                        
                                                    <td class="text-center">{{ $spam->site_name }}</td>
                                                    <td class="text-center">{{ OperationTrait::setDateTime($spam, "time") }}</td>
                                                    <td class="text-center">{{ $spam->final_service_type }}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-{{ $spam->is_round_trip == 1 ? 'success' : 'danger' }}" type="button">{{ $spam->is_round_trip == 1 ? "SI" : "NO" }}</button>
                                                    </td>
                                                    <td class="text-center"><?=OperationTrait::renderServiceStatus($spam)?></td>
                                                    <td class="text-center">
                                                        @if (RoleTrait::hasPermission(38))
                                                            <a href="/reservations/detail/{{ $spam->reservation_id }}"><p class="mb-1">{{ $spam->code }}</p></a>
                                                        @else
                                                            <p class="mb-1">{{ $spam->code }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">{{ $spam->full_name }}</td>
                                                    <td class="text-center">{{ trim($spam->client_phone) }}</td>
                                                    <td class="text-center">{{ trim(strtolower($spam->client_email)) }}</td>
                                                    <td class="text-center">{{ $spam->service_type_name }}</td>
                                                    <td class="text-center" class="text-center">{{ $spam->passengers }}</td>
                                                    <td class="text-center">{{ OperationTrait::setFrom($spam, "name") }}</td>
                                                    <td class="text-center">{{ OperationTrait::setTo($spam, "name") }}</td>
                                                    <td class="text-center">{{ number_format($spam->total_sales,2) }}</td>
                                                    <td class="text-center">{{ $spam->currency }}</td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>                    
    </div>

    <x-modals.filters.bookings :data="$data" />
@endsection