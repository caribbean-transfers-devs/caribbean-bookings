@php
    use App\Traits\RoleTrait;    
@endphp

@if(sizeof($media) > 0)
    @foreach($media as $key => $value)
        @php
            $extension = pathinfo($value->url, PATHINFO_EXTENSION);
        @endphp

        <div class="item">
            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ $value->url }}">
            @elseif(strtolower($extension) == 'pdf')                
                <embed src="https://docs.google.com/gview?embedded=true&url={{ $value->url }}" type="application/pdf" width="100%"> 
            @endif
            <p>{{ $value->path }}</p>
            <div class="btn_">
                @if (RoleTrait::hasPermission(65))
                    <a href="{{ $value->url }}" class="btn btn-primary btn-sm" data-id="{{ $value->id }}" data-name="{{ $value->path }}" target="_blank">Ver</a>
                @endif
                @if (RoleTrait::hasPermission(66))
                    <button class="btn btn-danger btn-sm deleteMedia" data-id="{{ $value->id }}">Eliminar</button>
                @endif
            </div>
        </div>
    @endforeach
@endif