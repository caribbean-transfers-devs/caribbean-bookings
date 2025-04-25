@php
    $phone = str_replace(" ", "",$reservation->client_phone);
    $phone = str_replace("(", "",$reservation->client_phone);
    $phone = str_replace(")", "",$reservation->client_phone);
    $phone = str_replace(".", "",$reservation->client_phone);
    // dump($reservation->toArray());
@endphp
<table class="table table-hover table-striped table-bordered table-details-booking mb-0">
    <tbody>        
        <tr>
            <th>Sitio</th>
            <td>{{ $reservation->site->name }}</td>
        </tr>
        <tr>
            <th>Pago al llegar</th>
            <td>{{ $reservation->pay_at_arrival }}</td>
        </tr>        
        <tr>
            <th>Nombre</th>
            <td>{{ $reservation->client_first_name }} {{ $reservation->client_last_name }}</td>
        </tr>
        <tr>
            <th>E-mail</th>
            <td>{{ $reservation->client_email }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td><a href="https://wa.me/{{ $phone }}" target="_blank" title="{{ $reservation->client_phone }}">{{ $reservation->client_phone }}</a></td>
        </tr>
        <tr>
            <th>Moneda</th>
            <td>{{ $reservation->currency }}</a></td>
        </tr>        
        <tr>
            <th>Desde</th>
            <td>{{ $reservation->items[0]->from_name }}</td>
        </tr>
        <tr>
            <th>Hacia</th>
            <td>{{ $reservation->items[0]->to_name }}</td>
        </tr>  
    </tbody>
</table>