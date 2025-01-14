@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use Illuminate\Support\Str;
    $total_general = 0;
    $total_conciliation = 0;
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
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12 layout-spacing">
            <div class="widget-content widget-content-area p-2 br-8">
                <div class="row">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-6">
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- <x-modals.filters.bookings :data="$data" :isSearch="1"  :vehicles="$vehicles" :zones="$zones" :websites="$websites" /> --}}
@endsection