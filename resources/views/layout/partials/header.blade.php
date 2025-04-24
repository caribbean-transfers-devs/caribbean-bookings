    <!--  BEGIN NAVBAR  -->
    <div class="header-container d-flex align-items-center">
        <header class="header navbar navbar-center navbar-expand-sm expand-header w-100">
            <ul class="navbar-item theme-brand flex-row  text-center">
                <li class="nav-item theme-logo">
                    <a href="/">
                        <img src="{{ asset("/assets/img/logos/brand.svg") }}" class="navbar-logo" alt="logo">
                    </a>
                </li>
            </ul>
            <form class="search-animated toggle-search" action="{{ route('management.reservations') }}" method="POST" accept-charset="utf-8" enctype="multipart/form-data">
                @csrf
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <div class="form-inline search-full form-inline search" role="search">
                    <div class="search-bar">
                        <input type="text" name="filter_text" value="{{ isset($data['filter_text']) ? $data['filter_text'] : '' }}" class="form-control search-form-control ml-lg-auto" placeholder="#/nombre/correo/telefono/Referencia">
                    </div>
                </div>
                <button type="submit" class="badge badge-secondary border-0">Buscar</button>
            </form>
            <ul class="navbar-item flex-row ms-auto action-area">

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar" src="{{ asset('/assets/img/profile-default.svg') }}" class="rounded-circle">{{ auth()->user()->id }}
                            </div>
                        </div>
                    </a>

                    <div class="dropdown-menu position-absolute" aria-labelledby="userProfileDropdown">
                        <div class="user-profile-section">
                            <div class="media mx-auto">
                                <div class="emoji me-2">
                                    &#x1F44B;
                                </div>
                                <div class="media-body">
                                    <h5>{{ auth()->user()->name }}</h5>
                                    <p>{{ auth()->user()->roles[0]->role->role }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown-item">
                            <a href="{{ route('logout') }}" class="__logouts">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-out"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg> 
                                <span>Cerrar Sesión</span>
                            </a>
                        </div>
                    </div>
                    
                </li>
            </ul>
        </header>
    </div>
    <!--  END NAVBAR  -->