
<div class="table-responsive table-wrapper">
    <table class="table custom-table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Código</th>                
                <th class="" scope="col">Nombre</th>
                <th class="" scope="col">Servicio</th>
                <th class="" scope="col">Pax</th>
                <th scope="col">Desde</th>
                <th scope="col">Hacia</th>
                <th class="text-center" scope="col">Total</th>
            </tr>
        </thead>
        <tbody>
            @if(sizeof($items) > 0)
                @foreach($items as $key => $value)
                    <tr>
                        <td>
                            <a href="/reservations/detail/{{ $value->reservation_id }}" class="btn btn-outline-dark" target="_blank">{{ $value->reservation_codes }}</a>
                        </td>
                        <td>{{ $value->full_name }}</td>
                        <td class="text-center">{{ $value->service_type_name }}</td>
                        <td class="text-center">{{ $value->passengers }}</td>
                        <td>{{ $value->from_name }}</td>
                        <td>{{ $value->to_name }}</td>
                        <td class="text-end">{{ $value->total_sales }} {{ $value->currency }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>