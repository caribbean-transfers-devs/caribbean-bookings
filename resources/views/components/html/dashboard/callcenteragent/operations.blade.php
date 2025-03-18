@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
    use App\Traits\OperationTrait;
    $data = [
        "total" => 0,
        "total_operated" => 0,
    ];
@endphp
<div class="table-responsive">
    <table class="table custom-table">
        <thead>
            <tr>
                <th class="text-center" scope="col">Tipo de servicio</th>
                <th class="text-center" scope="col">Código</th>
                <th class="text-center" scope="col">Fecha de reservación</th>
                <th class="text-center" scope="col">Sitio</th>
                <th class="text-center" scope="col">Estatus de reservación</th>
                <th class="text-center" scope="col">Estatus de pago</th>
                <th class="text-center" scope="col">Total de reservación</th>
                <th class="text-center" scope="col">Total de reservación MXN</th>
                <th class="text-center" scope="col">Total vendido por precio de servicio</th>
                <th class="text-center" scope="col">Moneda</th>
                <th class="text-center" scope="col">Nombre del cliente</th>
                <th class="text-center" scope="col">Desde</th>
                <th class="text-center" scope="col">Hacia</th>
                <th class="text-center" scope="col">Fecha de servicio</th>
                <th class="text-center" scope="col">Estatus de servicio</th>
                <th class="text-center" scope="col">Estatus de operación</th>
            </tr>
        </thead>
        <tbody>            
            @foreach($sales as $key => $booking)
                @php
                    $total = ( $booking->currency == "USD" ? $booking->total_sales * $exchange : $booking->total_sales );
                    $total_operated = ( $booking->currency == "USD" ? $booking->cost * $exchange : $booking->cost );
                    $data['total'] += $total;
                    $data['total_operated'] += $total_operated;
                @endphp
                <tr>
                    <td class="text-center"><span class="badge badge-{{ $booking->is_round_trip == 0 ? 'success' : 'danger' }} text-lowercase">{{ $booking->is_round_trip == 0 ? 'ONE WAY' : 'ROUND TRIP' }}</span></td>
                    <td class="text-center">
                        @if (RoleTrait::hasPermission(38))
                            <a href="/reservations/detail/{{ $booking->reservation_id }}"><?=$booking->code?></a>
                        @else
                            <?=$codes_string?>
                        @endif
                    </td>
                    <td class="text-center">{{ date("Y-m-d", strtotime($booking->created_at)) }}</td>
                    <td class="text-center">{{ $booking->site_name }}</td>
                    <td class="text-center"><button type="button" class="btn btn-{{ BookingTrait::classStatusBooking($booking->reservation_status) }}">{{ BookingTrait::statusBooking($booking->reservation_status) }}</button></td>
                    <td class="text-center" <?=BookingTrait::classStatusPayment($booking)?>>{{ BookingTrait::statusPayment($booking->payment_status) }}</td>
                    <td class="text-center">$ {{ number_format(round($booking->total_sales, 2),2) }}</td>
                    <td class="text-center">$ {{ number_format(round($total,2),2) }}</td>
                    <td class="text-center">$ {{ number_format(round($total_operated,2),2) }}</td>
                    <td class="text-center">{{ $booking->currency }}</td>                            
                    <td class="text-center">{{ $booking->full_name }}</td>
                    <td class="text-center">{{ OperationTrait::setFrom($booking, "name") }}</td>
                    <td class="text-center">{{ OperationTrait::setTo($booking, "name") }}</td>
                    <td class="text-center">{{ OperationTrait::setDateTime($booking, "date") }}</td>
                    <td class="text-center"><?=OperationTrait::renderServiceStatus($booking)?></td>
                    <td class="text-center"><?=OperationTrait::renderOperationStatus($booking)?></td>
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
                <th class="text-center">{{ number_format(round($data['total'],2),2) }}</th>
                <th class="text-center">{{ number_format(round($data['total_operated'],2),2) }}</th>
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