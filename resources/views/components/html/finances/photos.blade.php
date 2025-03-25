@if(sizeof($photos) > 0)
    @foreach($photos as $key => $value)
        @php
            $extension = pathinfo($value->url, PATHINFO_EXTENSION);
        @endphp

        <div class="item">
            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                <img src="{{ $value->url }}" alt="{{ $value->path }}">
            @elseif(strtolower($extension) == 'pdf')
                <embed src="https://docs.google.com/gview?embedded=true&url={{ $value->url }}" type="application/pdf" width="100%">
            @endif
            
            @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                <a class="container-degad" href="{{ $value->url }}" data-lightbox="galeria" data-title="{{ $value->path }}"></a>
            @elseif(strtolower($extension) == 'pdf')
                <a href="{{ $value->url }}" class="pdf-lightbox" href="{{ $value->url }}" data-title="{{ $value->path }}"></a>
            @endif

            <div class="content-top">
                <div class="btn_">
                    {{-- PERMITE ELIMINAR UNA IMAGEN --}}
                    @if (auth()->user()->hasPermission(66))
                        <button class="btn btn-danger btn-sm deleteMedia" data-id="{{ $value->id }}" data-name="{{ $value->path }}">Eliminar</button>
                    @endif
                    {{-- LA CATEGORIA DE LA IMAGEN --}}
                    <?=auth()->user()->renderCategoryPicture($value->type_media)?>
                </div>
            </div>
            <div class="content-bottom">
                <p>{{ $value->path }}</p>
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