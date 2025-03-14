@php
    use Carbon\Carbon;
@endphp
<div class="NewTimeLine">
    <ul>
        @foreach($followUps as $key => $followUp)
            <li>
                @php
                    $fecha = Carbon::parse($followUp->created_at);
                @endphp                
                <span>{{ date("Y/m/d H:i", strtotime($followUp->created_at)) }}</span> 
                <span>{{ $fecha->diffForHumans() }}</span>
                <div class="content">
                    <h3>{{ $followUp->name }}</h3>
                    <p>{{ $followUp->text }}</p>
                </div>
            </li>
        @endforeach
    </ul>
</div>