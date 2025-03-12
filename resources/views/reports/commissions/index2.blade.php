@php
    use App\Traits\FiltersTrait;
    $users = FiltersTrait::CallCenterAgent();
@endphp
@extends('layout.app')
@section('title') Reporte de comisiones @endsection

@push('Css')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="{{ mix('/assets/css/sections/reports/commissions.min.css') }}" rel="preload" as="style" >
    <link href="{{ mix('/assets/css/sections/reports/commissions.min.css') }}" rel="stylesheet" >
@endpush

@push('Js')
    <script src="{{ mix('/assets/js/sections/reports/commissions_new.min.js') }}"></script>
@endpush

@section('content')
    <div class="row layout-top-spacing callcenter-container">
        <div class="col-12 layout-spacing">
            <div class="items-button" id="filterModal">
                <div class="top">
                    <div class="item-input">
                        <div class="box_input transparent_border">
                            <svg width="24" height="24"><use xlink:href="{{ asset('/assets/img/icons/icons.svg#calendar') }}"></use></svg>
                            <div class="input">
                                <label for="filter_date">Fecha:</label>
                                <input type="text" name="date" id="filter_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        @if ( isset($users) && !empty($users) )
                            <select class="form-control selectpicker" title="Vendedor" data-live-search="true" data-selected-text-format="count > 1" name="user[]" id="user" data-value="{{ json_encode(( isset($data['user']) ? $data['user'] : [] )) }}" multiple data-actions-box="true">                            
                                @foreach ($users as $key => $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option> 
                                @endforeach
                            </select>
                        @endif
                        <select class="form-control selectpicker" title="Estatus" data-live-search="true" data-selected-text-format="count > 3" name="status[]" id="status" data-value="{{ json_encode(( isset($data['status']) ? $data['status'] : [] )) }}" multiple data-actions-box="true">                            
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                        <div class="box_button">
                            <label for="">Tipo de cambio comisiones:</label>
                            <strong id="exchangeInfo"></strong>
                        </div>
                    </div>
                </div>                
                {{-- <button class="btn btn-warning btn-sm">Tipo de cambio comisiones: </button> --}}
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-card-five">
                <div class="widget-content">
                    <div class="account-box">
                        <div class="info-box">
                            <div class="icon">
                                <span>
                                    <img src="https://designreset.com/cork/html/src/assets/img/money-bag.png" alt="money-bag">
                                </span>
                            </div>
                            <div class="balance-info">
                                <h6>Total de ventas</h6>
                                <p id="totalSales">0.00</p>
                            </div>
                        </div>
                        <div class="card-bottom-section">
                            <a href="javascript:void(0);" class="getData" data-type="sales">Ver Reporte</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-card-five">
                <div class="widget-content">
                    <div class="account-box">
                        <div class="info-box">
                            <div class="icon">
                                <span>
                                    <img src="https://designreset.com/cork/html/src/assets/img/money-bag.png" alt="money-bag">
                                </span>
                            </div>
                            <div class="balance-info">
                                <h6>Total de servicios operados</h6>
                                <p id="totalOperationSales">0.00</p>
                            </div>
                        </div>

                        <div class="card-bottom-section">
                            <a href="javascript:void(0);" class="getData" data-type="completed">Ver reporte</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-card-five">
                <div class="widget-content">
                    <div class="account-box">
                        <div class="info-box">
                            <div class="icon">
                                <span>
                                    <img src="https://designreset.com/cork/html/src/assets/img/money-bag.png" alt="money-bag">
                                </span>
                            </div>
                            <div class="balance-info">
                                <h6>Total de comisiones</h6>
                                <p id="totalCommissions">0.00</p>
                            </div>
                        </div>

                        <div class="card-bottom-section">
                            <a href="javascript:void(0);" class="getData" data-type="pending">Ver reporte</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-12 col-lg-12 col-md-9 col-sm-12 col-12 layout-spacing">
            <div class="widget widget-chart-one">
                <div class="widget-heading">
                    <h5 class="" id="titleCharts">Total</h5>
                    <div class="task-action">
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="#" role="button" id="renvenue" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal"><circle cx="12" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><circle cx="5" cy="12" r="1"></circle></svg>
                            </a>
                            <div class="dropdown-menu left" aria-labelledby="renvenue" style="will-change: transform;">
                                <a class="dropdown-item" href="javascript:void(0);" onclick="commissions.reloadSalesCharts()">Ventas</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="commissions.reloadSalesChartsSellers()">Ventas por vendedor</a>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="commissions.reloadSalesOperationCharts()">Operación por vendedor</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget-content">
                    <div class="text-center" id="revenueMonthly" style="max-height: 502px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- <x-modals.filters.bookings :users="$users" /> --}}
@endsection