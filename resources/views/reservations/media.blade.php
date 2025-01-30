@php
    use App\Traits\RoleTrait;
    use App\Traits\BookingTrait;
@endphp

@if(sizeof($media) > 0)
    @foreach($media as $key => $value)
        @php
            $extension = pathinfo($value->url, PATHINFO_EXTENSION);
        @endphp

        <div class="item">
            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                {{-- <a href="{{ $value->url }}" data-lightbox="galeria" data-title="{{ $value->path }}"> --}}
                    <img src="{{ $value->url }}" alt="{{ $value->path }}">
                {{-- </a> --}}
            @elseif(strtolower($extension) == 'pdf')
                <embed src="https://docs.google.com/gview?embedded=true&url={{ $value->url }}" type="application/pdf" width="100%"> 
            @endif
            <div class="container-degad">
                <div class="content-top">
                    <div class="btn_">
                        @if (RoleTrait::hasPermission(65))
                            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                                <a href="{{ $value->url }}" class="btn btn-primary btn-sm" href="{{ $value->url }}" data-lightbox="galeria" data-title="{{ $value->path }}">Ver</a>
                            @elseif(strtolower($extension) == 'pdf')
                                <a href="{{ $value->url }}" class="btn btn-primary btn-sm pdf-lightbox" href="{{ $value->url }}" data-title="{{ $value->path }}">Ver</a>
                            @endif
                        @endif
                        @if (RoleTrait::hasPermission(66))
                            <button class="btn btn-danger btn-sm deleteMedia" data-id="{{ $value->id }}" data-name="{{ $value->path }}">Eliminar</button>
                        @endif
                        <?=BookingTrait::renderCategoryPicture($value->type_media)?>
                    </div>                    
                </div>
                <div class="content-bottom">
                    <p>{{ $value->path }}</p>
                </div>
            </div>            
        </div>

        <!-- Contenedor del modal para PDF -->
        <div id="pdf-lightbox-modal" class="pdf-lightbox-modal">
            <div class="pdf-lightbox-content">
                <span class="pdf-lightbox-close">&times;</span>
                <iframe id="pdf-frame" src="" frameborder="0"></iframe>
            </div>
        </div>        
    @endforeach
@endif