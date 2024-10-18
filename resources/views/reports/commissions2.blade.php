@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;
    use Illuminate\Support\Str;
    $users = [];
    $exchange_rate = 16.50;
@endphp
@extends('layout.app')
@section('title') Comisiones @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/managment.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/reports/commissions.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
            array(  
                'text' => 'Filtrar',
                'className' => 'btn btn-primary __btn_create',
                'attr' => array(
                    'data-title' =>  "Filtro de reservaciones",
                    'data-bs-toggle' => 'modal',
                    'data-bs-target' => '#filterModal'
                )
            ),
            array(  
                'text' => 'Excel',
                'extend' => 'excelHtml5',
                'titleAttr' => 'Exportar como Excel',
                'className' => 'btn btn-primary',
            ),
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="col-xl-12 col-lg-12 col-sm-12  layout-spacing">
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
                <table id="zero-config" class="table table-rendering dt-table-hover" style="width:100%" data-button='<?=json_encode($buttons)?>'>
                    <thead>
                        <tr>
                            <th class="text-center">ID</th>
                            <th class="text-center">TIPO DE SERVICIO</th>
                            <th class="text-center">CÓDIGO</th>
                            <th class="text-center">FECHA DE RESERVACIÓN</th>
                            <th class="text-center">HORA DE RESERVACIÓN</th>
                            <th class="text-center">SITIO</th>
                            <th class="text-center">ESTATUS DE RESERVACIÓN</th>
                            <th class="text-center">TIPO DE SERVICIO EN OPERACIÓN</th>
                            <th class="text-center">NOMBRE DEL CLIENTE</th>
                            <th class="text-center">VEHÍCULO</th>
                            <th class="text-center">FECHA DE SERVICIO</th>
                            <th class="text-center">HORA DE SERVICIO</th>
                            <th class="text-center">ESTATUS DE SERVICIO</th>
                            <th class="text-center">ESTATUS DE OPERACIÓN</th>
                            <th class="text-center">VENDEDOR</th>
                            <th class="text-center">TOTAL DE RESERVACIÓN</th>
                            <th class="text-center">MONEDA</th>
                            <th class="text-center">MÉTODO DE PAGO</th> 
                            <th class="text-center">ESTATUS DE COMISIÓN</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @if(sizeof($operations) >= 1)
                            @foreach($operations as $key => $operation)
                                @php
                                    // dump($value);
                                    $status = $operation->reservation_status;

                                    if(!isset($users[Str::slug($operation->employee)])):
                                        $users[Str::slug($operation->employee)] = [
                                            'name' => $operation->employee,                                            
                                            'USD' => 0,
                                            'MXN' => 0,
                                            'QUANTITY' => 0,
                                            'bookings' => [],
                                        ];
                                    endif;
                                    if( !in_array($operation->reservation_id, $users[Str::slug($operation->employee)]['bookings']) ){
                                        array_push($users[Str::slug($operation->employee)]['bookings'], $operation->reservation_id);
                                    }
                                    $users[Str::slug($operation->employee)][$operation->currency] += $operation->total_sales;
                                    $users[Str::slug($operation->employee)]['QUANTITY']++;
                                @endphp
                                <tr class="" data-nomenclatura="{{ $operation->final_service_type }}{{ $operation->op_type }}" data-reservation="{{ $operation->reservation_id }}" data-item="{{ $operation->id }}" data-operation="{{ $operation->final_service_type }}" data-service="{{ $operation->operation_type }}" data-type="{{ $operation->op_type }}" data-close_operation="">
                                    <td class="text-center">{{ $operation->reservation_id }}</td>
                                    <td class="text-center"><span class="badge badge-{{ $operation->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $operation->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                                    <td class="text-center">
                                        @if (RoleTrait::hasPermission(38))
                                            <a href="/reservations/detail/{{ $operation->reservation_id }}"><p class="mb-1">{{ $operation->code }}</p></a>
                                        @else
                                            <p class="mb-1">{{ $operation->code }}</p>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ date("Y-m-d", strtotime($operation->created_at)) }}</td>
                                    <td class="text-center">{{ date("H:i", strtotime($operation->created_at)) }}</td>
                                    <td class="text-center">{{ $operation->site_name }}</td>
                                    <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($operation->reservation_status) }}">{{ BookingTrait::statusBooking($operation->reservation_status) }}</button></td>
                                    <td class="text-center">{{ $operation->final_service_type }}</td>
                                    <td class="text-center">{{ $operation->full_name }}</td>
                                    <td class="text-center">{{ $operation->service_type_name }}</td>
                                    <td class="text-center">{{ OperationTrait::setDateTime($operation, "date") }}</td>
                                    <td class="text-center">{{ OperationTrait::setDateTime($operation, "time") }}</td>
                                    <td class="text-center"><?=OperationTrait::renderServiceStatus($operation)?></td>
                                    <td class="text-center"><?=OperationTrait::renderOperationStatus($operation)?></td>
                                    <td class="text-center">{{ $operation->employee }}</td>                                    
                                    <td class="text-center">{{ number_format(($operation->total_sales),2) }}</td>
                                    <td class="text-center">{{ $operation->currency }}</td>
                                    <td class="text-center">{{ $operation->payment_type_name }} <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-info __payment_info bs-tooltip" title="Ver informacón detallada de los pagos" data-reservation="{{ $operation->reservation_id }}"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg></td>
                                    <td class="text-center">
                                        <span class="badge badge-{{ $operation->is_commissionable == 1 ? "success" : "danger" }}">{{ $operation->is_commissionable == 1 ? "SI" : "NO" }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
                <div class="mt-3 px-2">
                    <h6>Resumen</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>                                                        
                                    <th>Nombre</th>
                                    <th>Cantidad</th>
                                    <th>USD</th>
                                    <th>MXN</th>
                                    <th>Total</th>
                                    @if ( RoleTrait::hasPermission(96) )
                                        <th>Comisión</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @if(sizeof($users) >= 1)
                                    @foreach($users as $key => $value)
                                        @php
                                            $total = ($value['USD'] * $exchange_rate) + $value['MXN'];
                                            $commission = 0;
                                            if($total >= 50000 && $total <= 74999):
                                                //$commission = 2500;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 75000 && $total <= 99999):
                                                //$commission = 3750;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 100000 && $total <= 124999):
                                                //$commission = 6250;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 125000 && $total <= 174999):
                                                //$commission = 8750;
                                                $commission = 0.05 * $total;
                                            endif;
                                            if($total >= 175000):
                                                //$commission = 100000;
                                                $commission = 0.05 * $total;
                                            endif;
                                            
                                        @endphp
                                        <tr>
                                            <td>{{ $value['name'] }}</td>
                                            <td>{{ $value['QUANTITY'] }}</td>
                                            <td>{{ number_format($value['USD'],2) }}</td>
                                            <td>{{ number_format($value['MXN'],2) }}</td>
                                            <td>{{ number_format($total,2) }}</td>
                                            @if ( RoleTrait::hasPermission(96) )
                                                <td>{{ number_format($commission,2) }}</td>                                                
                                            @endif
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

    @php
        dump($users);
    @endphp
    <x-modals.reservations.reports :data="$search" />
    <x-modals.reservations.payments />
@endsection