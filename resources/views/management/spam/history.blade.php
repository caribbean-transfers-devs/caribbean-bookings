<div class="NewTimeLine">
    <ul>
        @foreach($items as $key => $value)
            <li>
                <span>{{ date("Y/m/d H:i", strtotime($value->created_at)) }}</span> 
                @if( $value->categories_reminder )
                    <img src="/assets/img/svg/bookmark.svg" width="25" height="25">
                @endif
                <div class="content">
                    <h3>{{ $value->title_name }}</h3>
                    <p>{{ $value->description }}</p>
                    <div>
                        <span class="badge badge-light-dark mb-2 me-1">{{ $value->user_create_name }}</span>
                        @if( $value->categories_reminder )
                            @if($value->categories_reminder_disable == 0) <span class="badge badge-danger">{{ $value->categories_reminder }}</span> @endif
                            @if($value->categories_reminder_disable == 1) <span class="badge badge-success">{{ $value->categories_reminder }}</span> @endif
                            @if($value->categories_reminder_disable == 1)
                                <div>
                                    <span class="badge badge-info">Resuelto por: {{ $value->user_resolve_name }} | {{ date("Y/m/d H:i", strtotime($value->categories_reminder_user_date)) }}</span> 
                                </div>
                            @endif
                        @endif                       
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
</div>