<div class="table-responsive">
    <table class="table custom-table table-hover table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Código</th>
                <th class="" scope="col">Nombre</th>                    
                <th scope="col">Desde</th>
                <th scope="col">Hacia</th>
                <th class="text-center" scope="col">Pax</th>
                <th class="text-center" scope="col">Seguimiento</th>
                <th class="text-center" scope="col">Estatus</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items[$status]['items'] as $key => $value)
                <tr>
                    <td class="text-center">
                        <button class="btn btn-outline-dark" onclick="spamOnenModal(event, {{ $value->rit_id }}, {{ $value->rez_id }},'{{ $value->spam }}')" style="width:100%;">{{ $value->code }}</button>
                    </td>
                    <td class="text-left">{{ $value->client_full_name }}</td>                    
                    <td class="text-left">{{ $value->from_name }}</td>
                    <td class="text-left">{{ $value->to_name }}</td>
                    <td class="text-center">{{ $value->passengers }}</td>

                    @if($value->last_date)
                        <td class="text-center bs-tooltip" title="{{$value->last_user}}">{{date("m/d H:i", strtotime($value->last_date))}}</td>                            
                    @else
                        <td class="text-center"><span class="badge badge-danger" style="font-size:7pt;">Sin seguimiento</span></td>                        
                    @endif

                    <td class="text-center">
                        @switch($status)
                            @case('PENDING')
                                    <span class="badge badge-primary" style="font-size:7pt">Pendiente</span>
                                @break
                            @case('CONFIRMED')
                                    <span class="badge badge-success" style="font-size:7pt">Confirmado</span>
                                @break
                            @case('SENT')
                                    <span class="badge badge-secondary" style="font-size:7pt">Enviado</span>
                                @break
                            @case('LATER')
                                    <span class="badge badge-dark" style="font-size:7pt">Después</span>
                                @break
                            @case('REJECTED')
                                    <span class="badge badge-danger" style="font-size:7pt">Rechazado</span>
                                @break
                            @default                                
                        @endswitch                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>