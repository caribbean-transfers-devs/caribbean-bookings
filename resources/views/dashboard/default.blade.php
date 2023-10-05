@extends('layout.master')
@section('title') Dashboard @endsection

@push('up-stack')    
@endpush

@push('bootom-stack')
@endpush

@section('content')
    <div class="container-fluid p-0">

        <div class="row">
            <div class="col-sm-12 col-xl-12">
                <div class="alert alert-primary alert-dismissible" role="alert">                    
                    <div class="alert-message">
                        <h4 class="alert-heading"><strong>Caribbean Transfers System</strong></h4>
                        <p>Bienvenido al sistema de reservaciones de &copy;Caribbean Transfers, para soporte y aclaraciones no dude en contactarnos por correo:</p>
                        <pre class="h6 text-danger mb-0">development@caribbean-transfers.com</pre>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection