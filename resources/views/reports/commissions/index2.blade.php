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
    <script src="https://cdn.jsdelivr.net/npm/@easepick/datetime@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/base-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@easepick/range-plugin@1.2.1/dist/index.umd.min.js"></script>
    <script src="{{ mix('/assets/js/sections/reports/commissions_new.min.js') }}"></script>
@endpush

@section('content')
    <div class="row layout-top-spacing callcenter-container">
        <div class="col-12 layout-spacing">
            <div class="items-button">
                <button class="btn btn-primary btn-sm __btn_create" data-title="Filtros de callcenter" data-bs-toggle="modal" data-bs-target="#filterModal"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" name="filter" class=""><path fill="" fill-rule="evenodd" d="M5 7a1 1 0 000 2h14a1 1 0 100-2H5zm2 5a1 1 0 011-1h8a1 1 0 110 2H8a1 1 0 01-1-1zm3 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd"></path></svg> Filtros</button>
                <button class="btn btn-success btn-sm">Fecha: <strong id="dateInfo"></strong></button>
                <button class="btn btn-warning btn-sm">Tipo de cambio comisiones: <strong id="exchangeInfo"></strong></button>
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

    <x-modals.filters.bookings :users="$users" />
@endsection