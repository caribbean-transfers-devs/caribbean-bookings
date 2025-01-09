<div class="col-xl-12 col-lg-12 col-md-12  mb-3">
    <a href="#" onclick="getSpamByStatus('CONFIRMED','{{ $date }}')">
        <div class="card bg-success">
            <div class="card-body pt-3">
                <h5 class="card-title mb-3" style="font-size:11pt">Confirmado</h5>
                <p class="card-text">{{ $items['CONFIRMED']['total'] }}</p>
            </div>
        </div>
    </a>
</div>

<div class="col-xl-3 col-lg-3 col-md-3 mb-3">
    <a href="#" onclick="getSpamByStatus('PENDING','{{ $date }}')">
        <div class="card bg-primary">
            <div class="card-body pt-3">
                <h5 class="card-title mb-3" style="font-size:11pt">Pendiente</h5>
                <p class="card-text">{{ $items['PENDING']['total'] }}</p>
            </div>
        </div>
    </a>
</div>


<div class="col-xl-3 col-lg-3 col-md-3  mb-3">
    <a href="#" onclick="getSpamByStatus('SENT','{{ $date }}')">
        <div class="card bg-secondary">
            <div class="card-body pt-3">
                <h5 class="card-title mb-3" style="font-size:11pt">Enviado</h5>
                <p class="card-text">{{ $items['SENT']['total'] }}</p>
            </div>
        </div>
    </a>
</div>

<div class="col-xl-3 col-lg-3 col-md-3  mb-3">
    <a href="#" onclick="getSpamByStatus('LATER','{{ $date }}')">
        <div class="card bg-dark">
            <div class="card-body pt-3">
                <h5 class="card-title mb-3" style="font-size:11pt">Después</h5>
                <p class="card-text">{{ $items['LATER']['total'] }}</p>
            </div>
        </div>
    </a>
</div>                       

<div class="col-xl-3 col-lg-3 col-md-3  mb-3">
    <a href="#" onclick="getSpamByStatus('REJECTED','{{ $date }}')">
        <div class="card bg-danger">
            <div class="card-body pt-3">
                <h5 class="card-title mb-3" style="font-size:11pt">Rechazado</h5>
                <p class="card-text">{{ $items['REJECTED']['total'] }}</p>
            </div>
        </div>
    </a>
</div>

<div class="col-12" id="spam-items-content">
    <div class="table-responsive">
        <table class="table table-hover table-striped table-bordered">
            <thead>
                <tr>
                    <th scope="col">Código</th>
                    <th class="" scope="col">Nombre</th>                    
                    <th scope="col">Desde</th>
                    <th scope="col">Hacia</th>
                    <th class="text-center" scope="col">Seguimiento</th>
                    <th class="text-center" scope="col">Estatus</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items[$status]['items'] as $key => $value)                    
                    <tr>
                        <td class="text-center">
                            <button class="btn btn-outline-dark _effect--ripple waves-effect waves-light" onclick="spamOnenModal(event, {{ $value->rit_id }}, {{ $value->rez_id }},'{{ $value->spam }}')" style="width:100%;">{{ $value->code }}</button>
                        </td>
                        <td class="text-left">{{ $value->client_full_name }}</td>                    
                        <td class="text-left">{{ $value->from_name }}</td>
                        <td class="text-left">{{ $value->to_name }}</td>

                        @if($value->last_date)
                            <td class="text-center bs-tooltip" title="{{$value->last_user}}">{{date("m/d H:i", strtotime($value->last_date))}}</td>
                        @else
                            <td class="text-center"><span class="badge badge-light-danger" style="font-size:7pt;">Sin seguimiento</span></td>
                        @endif

                        <td class="text-center">
                            @switch($value->spam)
                                @case('PENDING')
                                        <span class="badge badge-light-primary" style="font-size:7pt">Pendiente</span>
                                    @break
                                @case('CONFIRMED')
                                        <span class="badge badge-light-success" style="font-size:7pt">Confirmado</span>
                                    @break
                                @case('SENT')
                                        <span class="badge badge-light-secondary" style="font-size:7pt">Enviado</span>
                                    @break
                                @case('LATER')
                                        <span class="badge badge-light-dark" style="font-size:7pt">Después</span>
                                    @break
                                @case('REJECTED')
                                        <span class="badge badge-light-danger" style="font-size:7pt">Rechazado</span>
                                    @break
                                @default                                
                            @endswitch 
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
