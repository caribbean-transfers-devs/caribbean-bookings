<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Bookings</title>
	<meta name="description" content="Caribbean Transfers | Login">
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="/assets/img/icons/icon-48x48.png">
	<meta name='robots' content='noindex,follow' />

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="{{ mix('/assets/css/core/core.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/core/core.min.css') }}" rel="stylesheet" >
    <link href="{{ mix('/assets/css/panel/panel2.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/panel/panel2.min.css') }}" rel="stylesheet" >
    <link href="{{ mix('/assets/css/panel/panel.min.css') }}"rel="preload" as="style" >
    <link href="{{ mix('/assets/css/panel/panel.min.css') }}"rel="stylesheet" >	
	<style>
		body{
			background-color: #16161d;
			color: #ffffff;			
		}

		.auth-container{
			height: 100vh;
		}

		.card{
			background: #16161d;
			border-radius: 8px;
			display: grid;
			gap: 20px;
			grid-template-columns: 1fr;
			max-width: 570px;
			padding: 20px;
			width: 100%;
			border: 0;
			color: #ffffff;	
		}
		.card-body {
			padding: 0
		}

		.form-control{
			color: #000000;
		}
		.form-group label, label{
			color: #ffffff;
		}
		.btn, .btn:hover{
			font-size: 14px;
			background-color: #fb5607b8;
			border-color: #fb5607b8;
			box-shadow: none;
		}
	</style>
</head>
<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">

    <div class="auth-container d-flex">
        <div class="container mx-auto align-self-center">
            <div class="row">
    
                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center mx-auto">
                    <div class="card mt-3 mb-3">
                        <div class="card-body">
                            <div class="row">
								<div class="text-center">
									{{-- <img src="/assets/img/logos/brand.svg" alt="Caribbean Transfers" class="img-fluid" width="132" height="132"> --}}
									<img src="https://caribbean-transfers.com/assets/img/logo.svg" width="200" height="100" loading="lazy" alt="Logo | Caribbean Transfers" title="Logo | Caribbean Transfers">
								</div>
								@if ($errors->any())
									<div class="alert alert-danger mt-3" role="alert">
										<div class="alert-message">
											{{ $errors->first() }}
										</div>
									</div>
								@endif

								<form id="log-in-form" method="POST" action="/login">
									@csrf
									<div class="col-md-12">
										<div class="mb-3">
											<label class="form-label">Email</label>
											<input class="form-control" type="email" name="email" placeholder="Su email" value="{{ old('email') }}" autocomplete="username">
										</div>
									</div>
									<div class="col-12">
										<div class="mb-4">
											<label class="form-label">Contraseña</label>
											<input type="password" class="form-control" name="password" placeholder="Su contraseña" autocomplete="current-password">
										</div>
									</div>
									<div class="col-12">
										<div class="mb-3">
											<div class="form-check form-check-primary form-check-inline">
												<input class="form-check-input me-3" type="checkbox" value="remember-me" name="remember-me" checked id="form-check-default">
												<label class="form-check-label" for="form-check-default">
													Remember me
												</label>
											</div>
										</div>
									</div>
									
									<div class="col-12">
										<div class="mb-4">
											<button 
                                                class="g-recaptcha btn btn-secondary btn-lg w-100"
                                                data-sitekey="{{ config('services.gcaptcha.key')}}" 
                                                data-callback="onSubmit"
                                                data-action = 'submit'>Iniciar Sesión</button>											
										</div>
									</div>
								</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>

    <script src="{{ mix('/assets/js/core/core.min.js') }}"></script>
    <script src="{{ mix('/assets/js/panel/panel_custom.min.js') }}"></script>	
	{{-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> --}}
    <script>
        function onSubmit(token) {
            document.getElementById("log-in-form").submit();
        }
    </script>
</body>
</html>