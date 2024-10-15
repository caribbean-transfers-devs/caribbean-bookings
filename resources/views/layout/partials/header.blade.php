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
            <ul class="navbar-item flex-row ms-auto action-area">

                <li class="nav-item dropdown user-profile-dropdown  order-lg-0 order-1">
                    <a href="javascript:void(0);" class="nav-link dropdown-toggle user" id="userProfileDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <div class="avatar-container">
                            <div class="avatar avatar-sm avatar-indicators avatar-online">
                                <img alt="avatar" src="/assets/img/profile-default.svg" class="rounded-circle">
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
                                    <h5>{{ isset(Session('token_data')['name']) ? Session('token_data')['name'] : ( config('app.locale') == "es" ? "Indefinido" : "Undefined" ) }}</h5>
                                    <p>{{ isset(Session('token_data')['package']) && Session('token_data')['package'] != NULL ? Session('token_data')['package']['name_'.config('app.locale')] : ( config('app.locale') == "es" ? "Indefinido" : "Undefined" ) }}</p>
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