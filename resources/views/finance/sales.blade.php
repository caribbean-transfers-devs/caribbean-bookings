@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use Illuminate\Support\Str;
    $total_general = 0;
    $total_conciliation = 0;
    $total_general2 = 0;
    $total_conciliation2 = 0;

    $total = 0;
@endphp
@extends('layout.app')
@section('title') Reporte De Ventas @endsection

@push('Css')
    <link href="{{ mix('/assets/css/sections/report_sales.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/report_sales.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/reports/sales.min.js') }}"></script>
@endpush

@section('content')
    @php
        $buttons = array(
        );
    @endphp
    <div class="row layout-top-spacing">
        <div class="layout-top-spacing widget-content widget-content-area br-8 mb-3 p-2">
            <button class="btn btn-primary _btn_create" data-title="Filtros" data-bs-toggle="modal" data-bs-target="#filterModal"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros</button>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget-content widget-content-area p-2 br-8">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
                                <div class="card">
                                    {{ count($paypal) }} - PAYPAL
                                </div>
                                <div class="table-responsive">
                                    <table id="data" class="table dt-table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">CODE</th>
                                                <th class="text-center">SERVICE</th>
                                                <th class="text-center">MONEDA</th>
                                                <th class="text-center">VENTA</th>
                                                <th class="text-center">VENTA CONCILIADA</th>
                                                <th class="text-center">PENDIENTE DE CONCILIAR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(sizeof($paypal) >= 1)
                                                @foreach ($paypal as $item)
                                                    @php
                                                        $total_general += ( $item->currency == "USD" ? $item->total_sales * $exchange : $item->total_sales );
                                                        $total_conciliation += ( $item->is_conciliated == 1 ? number_format(( $item->currency == "USD" ? $item->total * $exchange : $item->total ),2) : 0 );
                                                    @endphp
                                                    <tr style="{{ ( $item->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}">                                    
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
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($item->reservation_status) }} mb-1">{{ BookingTrait::statusBooking($item->reservation_status) }}</button>
                                                            <span class="badge badge-{{ $item->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $item->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>                                        
                                                        </td>
                                                        <td class="text-center">{{ $item->currency }}</td>
                                                        <td class="text-center">{{ number_format(( $item->currency == "USD" ? $item->total_sales * $exchange : $item->total_sales ),2) }}</td>
                                                        <td class="text-center">{{ ( $item->is_conciliated == 1 ? number_format(( $item->currency == "USD" ? $item->total * $exchange : $item->total ),2) : 0 ) }}</td> 
                                                        <td class="text-center">{{ 0 }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <th class="text-center"></th>
                                            <th class="text-center"></th>
                                            <th class="text-center"></th>
                                            <th class="text-center">{{ number_format($total_general,2) }}</th>
                                            <th class="text-center">{{ number_format($total_conciliation,2) }}</th>
                                            <th class="text-center">0</th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card">
                                    {{ count($paypal2) }} - STRIPE
                                </div>                                
                                <div class="table-responsive">
                                    <table id="data" class="table dt-table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">CODE</th>
                                                <th class="text-center">SERVICE</th>
                                                <th class="text-center">MONEDA</th>
                                                <th class="text-center">VENTA</th>
                                                <th class="text-center">VENTA CONCILIADA</th>
                                                <th class="text-center">PENDIENTE DE CONCILIAR</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(sizeof($stripe) >= 1)
                                                @foreach ($stripe as $item2)
                                                    @php
                                                        $total_general2 += ( $item2->currency == "USD" ? $item2->total_sales * $exchange : $item2->total_sales );
                                                        // $total_conciliation2 += ( $item2->is_conciliated == 1 ? number_format(( $item2->currency == "USD" ? $item2->total * $exchange : $item2->total ),2) : 0 );
                                                    @endphp
                                                    <tr style="{{ ( $item2->is_today != 0 ? 'background-color: #fcf5e9;' : '' ) }}">
                                                        <td class="text-center">
                                                            @php
                                                                $codes_string = "";
                                                                $codes = explode(",",$item2->reservation_codes);
                                                                foreach ($codes as $key => $code) {
                                                                    $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                                                }
                                                            @endphp
                                                            @if (RoleTrait::hasPermission(38))
                                                                <a href="/reservations/detail/{{ $item2->reservation_id }}"><?=$codes_string?></a>
                                                            @else
                                                                <?=$codes_string?>
                                                            @endif
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($item2->reservation_status) }} mb-1">{{ BookingTrait::statusBooking($item2->reservation_status) }}</button>
                                                            <span class="badge badge-{{ $item2->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $item2->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span>                                        
                                                        </td>
                                                        <td class="text-center">{{ $item2->currency }}</td>
                                                        <td class="text-center">{{ number_format(( $item2->currency == "USD" ? $item2->total_sales * $exchange : $item2->total_sales ),2) }}</td>
                                                        <td class="text-center">{{ 0 }}</td> 
                                                        <td class="text-center">{{ 0 }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <th class="text-center"></th>
                                            <th class="text-center"></th>
                                            <th class="text-center"></th>
                                            <th class="text-center">{{ number_format($total_general2,2) }}</th>
                                            <th class="text-center">{{ number_format($total_conciliation2,2) }}</th>
                                            <th class="text-center">0</th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="card">
                                    {{ count($payments) }}
                                </div>                                
                                <div class="table-responsive">
                                    <table id="data" class="table dt-table-hover">
                                        <thead>
                                            <tr>
                                                <th class="text-center">CODE</th>
                                                <th class="text-center">MONEDA</th>
                                                <th class="text-center">VENTA</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(sizeof($payments) >= 1)
                                                @foreach ($payments as $item3)
                                                    @php
                                                        $total += ( $item3->currency_payment == "USD" ? $item3->total * $exchange : $item3->total );
                                                    @endphp
                                                    <tr>
                                                        <th class="text-center">{{ $item3->reservation_id }}</th>
                                                        <td class="text-center">{{ $item3->currency_payment }}</td>
                                                        <td class="text-center">{{ number_format(( $item3->currency_payment == "USD" ? $item3->total * $exchange : $item3->total ),2) }}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                        <tfoot>
                                            <th class="text-center"></th>
                                            <th class="text-center"></th>
                                            <th class="text-center">{{ number_format($total,2) }}</th>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <x-modals.filters.bookings :data="$data" />
@endsection