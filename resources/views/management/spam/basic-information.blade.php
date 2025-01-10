@php
    $phone = str_replace(" ", "",$item->client_phone);
    $phone = str_replace("(", "",$item->client_phone);
    $phone = str_replace(")", "",$item->client_phone);
    $phone = str_replace(".", "",$item->client_phone);
@endphp
<table class="table table-hover table-striped table-bordered table-details-booking mb-0">
    <tbody>
        <tr>
            <th>Sitio</th>
            <td>{{ $item->site_name }}</td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td>{{ $item->client_full_name }}</td>
        </tr>
        <tr>
            <th>E-mail</th>
            <td>{{ $item->client_email }}</td>
        </tr>
        <tr>
            <th>Teléfono</th>
            <td><a href="https://wa.me/{{ $phone }}" target="_blank" title="{{ $item->client_phone }}">{{ $item->client_phone }}</a></td>
        </tr>
        <tr>
            <th>Total</th>
            <td>${{ number_format($item->total_sales,2) }} {{ $item->currency }}</td>
        </tr>
        <tr>
            <th>Desde</th>
            <td>{{ $item->from_name }}</td>
        </tr>
        <tr>
            <th>Hacia</th>
            <td>{{ $item->to_name }}</td>
        </tr>
        <tr>
            <th>Operación</th>
            <td>{{ date("Y/m/d H:i", strtotime($item->op_one_pickup)) }}</td>
        </tr>
        <tr>
            <th>Estatus de OP.</th>
            <td>{{ $item->op_one_status }}</td>
        </tr>  
    </tbody>
</table>