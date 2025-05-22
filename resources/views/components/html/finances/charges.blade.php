@php
    use Carbon\Carbon;
@endphp
{{-- @dump($charge) --}}
<table class="table table-hover table-striped table-bordered table-details-booking mb-3">
    <tbody>        
        <tr>
            <th class="text-left">ID</th>
            <td>{{ isset($charge['id']) ? $charge['id'] : 'SIN ID' }}</td>
        </tr>
        <tr>
            <th class="text-left">Reservación</th>
            <td>{{ isset($charge['metadata']['reservation_id']) ? $charge['metadata']['reservation_id'] : 'NO TIENE UNA RESERVA RELACIONADA' }}</td>
        </tr>        
        <tr>
            <th class="text-left">Fecha de transacción</th>
            <td>{{ isset($charge['created']) ? Carbon::parse($charge['created'])->format('Y-m-d') : 'SIN FECHA' }}</td>
        </tr>
        <tr>
            <th class="text-left">Fecha de disponibilidad de fondos</th>
            <td>{{ isset($charge['balance_transaction']['available_on']) ? Carbon::parse($charge['balance_transaction']['available_on'])->format('Y-m-d') : 'SIN FECHA' }}</td>
        </tr>
        <tr>
            <th class="text-left">Estatus de disponibilidad de fondos</th>
            <td class="text-uppercase">
                @if ( isset($charge['balance_transaction']['status']) )
                    @if ( $charge['balance_transaction']['status'] == "available" )
                        <span class="badge badge-success text-uppercase">disponible</span>
                    @else
                        <span class="badge badge-warning text-uppercase">pendiente</span>
                    @endif
                @else
                    NO TIENE ESTATUS DEFINIDO
                @endif
            </td>
        </tr>

        {{-- datos de cliente --}}

        <tr>
            <th class="text-left">Nombre</th>
            <td>{{ isset($charge['billing_details']['name']) ? $charge['billing_details']['name'] : 'SIN NOMBRE' }}</td>
        </tr>
        <tr>
            <th class="text-left">E-mail</th>
            <td>{{ isset($charge['billing_details']['email']) ? $charge['billing_details']['email'] : 'SIN CORREO' }}</td>
        </tr>
        <tr>
            <th class="text-left">Teléfono</th>
            <td>{{ isset($charge['billing_details']['phone']) ? $charge['billing_details']['phone'] : 'SIN TELÉFONO' }}</td>
        </tr>
    </tbody>
</table>

<h6 class="mb-3 fw-bold text-uppercase">Información de cobro</h6>
<table class="table table-hover table-striped table-bordered table-details-booking mb-3">
    <tbody>
        <tr>
            <th class="text-left">Metodo de pago</th>
            <td class="text-uppercase">{{ ( isset($charge['payment_method_details']['type']) ? $charge['payment_method_details']['type'] : 'SIN METOD DE PAGO' ) }}</td>
        </tr>        
        <tr>
            <th class="text-left">Total Cobrado</th>
            <td class="text-uppercase">$ {{ number_format(( isset($charge['balance_transaction']['amount']) ? ($charge['balance_transaction']['amount']/100) : 0 ), 2) }} {{ isset($charge['currency']) ? $charge['currency'] : 'SIN MONEDA' }}</td>
        </tr>
        <tr>
            <th class="text-left">Comisión De Stripe</th>
            <td class="text-uppercase">$ {{ number_format(( isset($charge['balance_transaction']['fee']) ? ($charge['balance_transaction']['fee']/100) : 0 ), 2) }} {{ isset($charge['currency']) ? $charge['currency'] : 'SIN MONEDA' }}</td>
        </tr>
        <tr>
            <th class="text-left">Total neto</th>
            <td class="text-uppercase">$ {{ number_format(( isset($charge['balance_transaction']['net']) ? ($charge['balance_transaction']['net']/100) : 0 ), 2) }} {{ isset($charge['currency']) ? $charge['currency'] : 'SIN MONEDA' }}</td>
        </tr>
        <tr>
            <th class="text-left">Recibo</th>
            <td><a href="{{ isset($charge['receipt_url']) ? $charge['receipt_url'] : '' }}">{{ isset($charge['receipt_url']) ? '#'.$charge['receipt_number'] : 'Sin recibo de cobro' }}</a></td>
        </tr>
    </tbody>
</table>

@if ( $charge['refunded'] )
    <h6 class="mb-3 fw-bold text-uppercase">Listado de reembolsos</h6>
    <table class="table table-hover table-details-booking">
        <thead>
            <tr>
                <th>ID Reembolso</th>
                <th>Balance ID</th>
                <th>Monto</th>
                <th class="text-center">Moneda</th>
                <th class="text-center">Fecha</th>
                <th class="text-center">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($charge['refunds']['data'] as $refund)
                <tr>
                    <td>{{ $refund['id'] }}</td>
                    <td>{{ $refund['balance_transaction'] }}</td>
                    <td>$ {{ number_format(($refund['amount']/100),2) }}</td>
                    <td class="text-center text-uppercase">{{ $refund['currency'] }}</td>
                    <td class="text-center">{{ Carbon::parse($refund['created'])->format('Y-m-d') }}</td>
                    <td class="text-center"><span class="badge badge-{{ $refund['status'] == "succeeded" ? 'success' : 'warning' }} text-uppercase">{{ $refund['status'] == "succeeded" ? 'Exitoso' : 'Pendiente' }}</span></td>
                </tr>
            @endforeach            
        </tbody>
    </table>
@endif

<div>
    {{-- <iframe src="https://docs.google.com/gview?embedded=true&url={{ $charge['receipt_url'] }}" id="frontBadge" class="" width="100%" height="600px" style="border: none;"></iframe> --}}
</div>