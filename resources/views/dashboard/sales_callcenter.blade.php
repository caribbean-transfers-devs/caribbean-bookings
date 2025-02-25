@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    $data = [
        "total" => 0,
    ];
@endphp
<div class="row">
    {{-- <div class="col-xl-12 col-lg-12 col-md-12 mb-3">
        <div class="row">
            <div class="col-xl-3">
                <div class="card bg-info p-3">
                    <h5 class="card-title" style="font-size:11pt;">Total de reservas: {{ count($sales) }}</h5>
                </div>
            </div>
            <div class="col-xl-3">
                <div class="card bg-success p-3">
                    <h5 class="card-title" style="font-size:11pt;">Total de vendido: {{ $data['total'] }}</h5>
                </div>
            </div>
        </div>
    </div> --}}
    <div class="col-xl-12 col-lg-12 col-md-12">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-center" scope="col">Tipo de servicio</th>
                        <th class="text-center" scope="col">Código</th>
                        <th class="text-center" scope="col">Fecha de reservación</th>
                        <th class="text-center" scope="col">Hora de reservación</th>
                        <th class="text-center" scope="col">Sitio</th>
                        <th class="text-center" scope="col">Estatus de reservación</th>
                        <th class="text-center" scope="col">Estatus de pago</th>
                        <th class="text-center" scope="col">Total de reservación</th>
                        <th class="text-center" scope="col">Total de reservación MXN</th>
                        <th class="text-center" scope="col">Moneda</th>
                        <th class="text-center" scope="col">Nombre del cliente</th>
                        <th class="text-center" scope="col">Desde</th>
                        <th class="text-center" scope="col">Hacia</th>
                        <th class="text-center" scope="col">Fecha de servicio</th>
                        <th class="text-center" scope="col">Hora de servicio</th>
                        <th class="text-center" scope="col">Estatus de servicio(s)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $key => $booking)
                        @php
                            $total = ( $booking->currency == "USD" ? $booking->total_sales * $exchange : $booking->total_sales );
                            $data['total'] += $total;
                        @endphp
                        <tr>
                            <td class="text-center"><span class="badge badge-{{ $booking->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $booking->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                            <td class="text-center">
                                @php
                                    $codes_string = "";
                                    $codes = explode(",",$booking->reservation_codes);
                                    foreach ($codes as $key => $code) {
                                        $codes_string .= '<p class="mb-1">'.$code.'</p>';
                                    }
                                @endphp
                                @if (RoleTrait::hasPermission(38))
                                    <a href="/reservations/detail/{{ $booking->reservation_id }}"><?=$codes_string?></a>
                                @else
                                    <?=$codes_string?>
                                @endif
                            </td>
                            <td class="text-center">{{ date("Y-m-d", strtotime($booking->created_at)) }}</td>
                            <td class="text-center">{{ date("H:i", strtotime($booking->created_at)) }}</td>
                            <td class="text-center">{{ $booking->site_name }}</td>
                            <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($booking->reservation_status) }}">{{ BookingTrait::statusBooking($booking->reservation_status) }}</button></td>
                            <td class="text-center" <?=BookingTrait::classStatusPayment($booking)?>>{{ BookingTrait::statusPayment($booking->payment_status) }}</td>
                            <td class="text-center">$ {{ number_format(round($booking->total_sales, 2),2) }}</td>
                            <td class="text-center">$ {{ number_format(round($total, 2),2) }}</td>
                            <td class="text-center">{{ $booking->currency }}</td>
                            <td class="text-center">{{ $booking->full_name }}</td>
                            <td class="text-center">{{ $booking->from_name }}</td>
                            <td class="text-center">{{ $booking->to_name }}</td>
                            <td class="text-center">
                                @php
                                    $pickup_from = explode(',',$booking->pickup_from);
                                    $pickup_to = explode(',',$booking->pickup_to);
                                @endphp
                                [{{ date("Y-m-d", strtotime($pickup_from[0])) }}]
                                @if ( $booking->is_round_trip != 0 )
                                    [{{ date("Y-m-d", strtotime($pickup_to[0])) }}]
                                @endif
                            </td>
                            <td class="text-center">
                                [{{ date("H:i", strtotime($pickup_from[0])) }}] <br>
                                @if ( $booking->is_round_trip != 0 )
                                    [{{ date("H:i", strtotime($pickup_to[0])) }}]
                                @endif
                            </td>
                            <td class="text-center">
                                <?=BookingTrait::renderServiceStatus($booking->one_service_status)?><br>
                                @if ( $booking->is_round_trip != 0 )
                                    <?=BookingTrait::renderServiceStatus($booking->two_service_status)?>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-center">Totales</th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center">{{ number_format(round($data['total'],2),2) }}</th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                        <th class="text-center"></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>        