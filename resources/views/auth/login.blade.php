<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <title>Bookings</title>
	<meta name="description" content="Caribbean Transfers - Bookings">
    <link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="shortcut icon" href="/assets/img/icons/icon-48x48.png">
	<meta name='robots' content='noindex,follow' />

    <link href="{{ mix('/assets/css/base/fonts.min.css') }}&family=Inter:wght@300;400;600&display=swap" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/base/fonts.min.css') }}&family=Inter:wght@300;400;600&display=swap" rel="stylesheet" >
    <link href="/assets/css/base/base.min.css" rel="preload" as="style" >
    <link href="/assets/css/base/base.min.css" rel="stylesheet" >
</head>
<body data-theme="default" data-layout="fluid" data-sidebar-position="left" data-sidebar-layout="default">
	<main class="d-flex w-100 h-100">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2">¡Bienvenido!</h1>
							<p class="lead">
								Inicie sesión en su cuenta para continuar
							</p>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-4">
									<div class="text-center">
										<img src="/assets/img/logos/brand.svg" alt="Caribbean Transfers" class="img-fluid" width="132" height="132">
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
										<div class="mb-3">
											<label class="form-label">Email</label>
											<input class="form-control form-control-lg" type="email" name="email" placeholder="Su email" value="{{ old('email') }}">
										</div>
										<div class="mb-3">
											<label class="form-label">Contraseña</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Su contraseña">
										</div>
										<div>
											<label class="form-check">
												<input class="form-check-input" type="checkbox" value="remember-me" name="remember-me" checked>
												<span class="form-check-label">
													Recuerdame
												</span>
											</label>
										</div>
										<div class="text-center mt-3">
											<button 
                                                class="g-recaptcha btn btn-primary btn-lg"
                                                data-sitekey="{{ config('services.gcaptcha.key')}}" 
                                                data-callback="onSubmit"
                                                data-action = 'submit'>Iniciar Sesión</button>
										</div>
									</form>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>

	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        function onSubmit(token) {
            document.getElementById("log-in-form").submit();
        }
    </script>
</body>

</html>