@props(['bookingsStatus','dataMethodPayments','dataCurrency','dataSites','dataOriginSale','dataVehicles','dataDestinations','dataUnit','dataDriver','dataServiceTypeOperation'])
<!-- Modal -->
<div class="modal fade" id="chartsModal" tabindex="-1" role="dialog" aria-labelledby="chartsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chartsModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body">
                    @if ( isset($bookingsStatus) )
                        <input type="hidden" id="bookingsStatus" value='@json(( isset($bookingsStatus['data']) ? $bookingsStatus['data'] : [] ))'>
                    @endif
                    @if ( isset($dataMethodPayments) )
                        <input type="hidden" id="dataMethodPayments" value='@json(( isset($dataMethodPayments['data']) ? $dataMethodPayments['data'] : [] ))'>
                    @endif
                    @if ( isset($dataCurrency) )
                        <input type="hidden" id="dataCurrency" value='@json(( isset($dataCurrency['data']) ? $dataCurrency['data'] : [] ))'>
                    @endif
                    @if ( isset($dataVehicles) )
                        <input type="hidden" id="dataVehicles" value='@json(( isset($dataVehicles['data']) ? $dataVehicles['data'] : [] ))'>
                    @endif
                    @if ( isset($dataServiceTypeOperation) )
                        <input type="hidden" id="dataServiceTypeOperation" value='@json(( isset($dataServiceTypeOperation['data']) ? $dataServiceTypeOperation['data'] : [] ))'>
                    @endif

                    @if ( isset($dataSites) )
                        <input type="hidden" id="dataSites" value='@json(( isset($dataSites['data']) ? $dataSites['data'] : [] ))'>
                    @endif
                    @if ( isset($dataDestinations) )
                        <input type="hidden" id="dataDestinations" value='@json(( isset($dataDestinations['data']) ? $dataDestinations['data'] : [] ))'>
                    @endif
                    @if ( isset($dataDriver) )
                        <input type="hidden" id="dataDriver" value='@json(( isset($dataDriver['data']) ? $dataDriver['data'] : [] ))'>
                    @endif                    
                    @if ( isset($dataUnit) )
                        <input type="hidden" id="dataUnit" value='@json(( isset($dataUnit['data']) ? $dataUnit['data'] : [] ))'>
                    @endif
                    @if ( isset($dataOriginSale) )
                        <input type="hidden" id="dataOriginSale" value='@json(( isset($dataOriginSale['data']) ? $dataOriginSale['data'] : [] ))'>
                    @endif
                {{-- <div class="box_filters">
                    <h6>Filtro por...</h6>
                </div> --}}
                <div class="box_container">
                    @if ( isset($bookingsStatus) )
                        <div id="chartStatus">
                            <h6>Por Estatus</h6>
                            <canvas class="chartSale" id="chartSaleStatus" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>ESTATUS</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bookingsStatus['data'] as $keyStatus => $status )
                                            <tr>
                                                <th>{{ $status['name'] }}</th>
                                                <td class="text-center">{{ number_format($status['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $status['counter'] }}</td>
                                                <td class="text-center">{{ number_format($status['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($status['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($bookingsStatus['gran_total'] - ( isset($bookingsStatus['data']['CANCELLED']) ? $bookingsStatus['data']['CANCELLED']['gran_total'] : 0 ) ,2) }}</th>
                                            <th class="text-center">{{ $bookingsStatus['counter'] }}</th>
                                            <th class="text-center">{{ number_format($bookingsStatus['MXN']['total'] - ( isset($bookingsStatus['data']['CANCELLED']) ? $bookingsStatus['data']['CANCELLED']['MXN']['total'] : 0 ),2) }}</th>
                                            <th class="text-center">{{ number_format($bookingsStatus['USD']['total'] - ( isset($bookingsStatus['data']['CANCELLED']) ? $bookingsStatus['data']['CANCELLED']['USD']['total'] : 0 ),2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataMethodPayments) )
                        <div id="chartMethodPayments">
                            <h6>Por Metodo De Pago</h6>
                            <canvas class="chartSale" id="chartSaleMethodPayments" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>METODO DE PAGO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataMethodPayments['data'] as $keyMethod => $method )
                                            <tr>
                                                <th>{{ $method['name'] }}</th>
                                                <td class="text-center">{{ number_format($method['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $method['counter'] }}</td>
                                                <td class="text-center">{{ number_format($method['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($method['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataMethodPayments['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataMethodPayments['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataMethodPayments['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataMethodPayments['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataCurrency) )
                        <div id="chartCurrency">
                            <h6>Por Moneda</h6>
                            <canvas class="chartSale" id="chartSaleCurrency" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>MONEDA</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">TOTAL</th>                                
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataCurrency['data'] as $keyCurrency => $currency )
                                            <tr>
                                                <th>{{ $currency['name'] }}</th>
                                                <td class="text-center">{{ number_format($currency['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $currency['counter'] }}</td>
                                                <td class="text-center">{{ number_format($currency['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataCurrency['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataCurrency['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataCurrency['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataVehicles) )
                        <div id="chartVehicle">
                            <h6>Por Vehículo</h6>
                            <canvas class="chartSale" id="chartSaleVehicle" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>VEHÍCULO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataVehicles['data'] as $keyVehicle => $vehicle )
                                            <tr>
                                                <th>{{ $vehicle['name'] }}</th>
                                                <td class="text-center">{{ number_format($vehicle['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $vehicle['counter'] }}</td>
                                                <td class="text-center">{{ number_format($vehicle['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($vehicle['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataVehicles['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataVehicles['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataVehicles['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataVehicles['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataServiceTypeOperation) )
                        <div id="chartServiceType">
                            <h6>Por Tipo De Servicio</h6>
                            <canvas class="chartSale" id="chartSaleServiceType" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>TIPO DE SERVICIO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataServiceTypeOperation['data'] as $keyTypeOperation => $typeoperation )
                                            <tr>
                                                <th>{{ $typeoperation['name'] }}</th>
                                                <td class="text-center">{{ number_format($typeoperation['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $typeoperation['counter'] }}</td>
                                                <td class="text-center">{{ number_format($typeoperation['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($typeoperation['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataServiceTypeOperation['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataServiceTypeOperation['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataServiceTypeOperation['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataServiceTypeOperation['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif                    

                    @if ( isset($dataSites) )
                        <div id="chartSites">
                            <h6>Por Sitio</h6>
                            <canvas class="chartSale" id="chartSaleSites" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>SITIO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataSites['data'] as $keySite => $site )
                                            <tr>
                                                <th>{{ $site['name'] }}</th>
                                                <td class="text-center">{{ number_format($site['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $site['counter'] }}</td>
                                                <td class="text-center">{{ number_format($site['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($site['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataSites['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataSites['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataSites['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataSites['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataDestinations) )
                        <div id="chartDestination">
                            <h6>Por Destino</h6>
                            <canvas class="chartSale" id="chartSaleDestination" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>DESTINO</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataDestinations['data'] as $keyDestination => $destination )
                                            <tr>
                                                <th>{{ $destination['name'] }}</th>
                                                <td class="text-center">{{ number_format($destination['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $destination['counter'] }}</td>
                                                <td class="text-center">{{ number_format($destination['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($destination['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataDestinations['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataDestinations['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataDriver) )
                        <div id="chartDrivers">
                            <h6>Por Conductor</h6>
                            <canvas class="chartSale" id="chartSaleDrivers" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-driver mb-0">
                                    <thead>
                                        <tr>
                                            <th>CONDUCTOR</th>
                                            <th>UNIDADES</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                            <th class="text-center">COMISIÓN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataDriver['data'] as $keyDriver => $driver )
                                            @php
                                                $unitsDriver = '[' . implode(',', $driver['units']) . ']';
                                            @endphp
                                            <tr>
                                                <th>{{ $driver['name'] }}</th>
                                                <td>{{ $unitsDriver }}</td>
                                                <td class="text-center">{{ number_format($driver['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $driver['counter'] }}</td>
                                                <td class="text-center">{{ number_format($driver['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($driver['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($driver['commission'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th></th>
                                            <th class="text-center">{{ number_format($dataDriver['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataDriver['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['commission'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataUnit) )
                        <div id="chartUnits">
                            <h6>Por Unidad</h6>
                            <canvas class="chartSale" id="chartSaleUnits" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>UNIDAD</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                            <th class="text-center">COSTO OPERATIVO</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataUnit['data'] as $keyUnit => $unit )
                                            <tr>
                                                <th>{{ $unit['name'] }}</th>
                                                <td class="text-center">{{ number_format($unit['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $unit['counter'] }}</td>
                                                <td class="text-center">{{ number_format($unit['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($unit['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($unit['operating_cost'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataUnit['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataUnit['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['operating_cost'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataOriginSale) )
                        <div id="chartOriginSale">
                            <h6>Por Origen De Venta</h6>
                            <canvas class="chartSale" id="chartSaleOriginSale" style="max-width: 100%; max-height: 400px;"></canvas>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>ORIGEN</th>
                                            <th class="text-center">GRAN TOTAL</th>
                                            <th class="text-center">CANTIDAD</th>
                                            <th class="text-center">PESOS</th>
                                            <th class="text-center">DOLARES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataOriginSale['data'] as $keyOrigin => $origin )
                                            <tr>
                                                <th>{{ $origin['name'] }}</th>
                                                <td class="text-center">{{ number_format($origin['gran_total'],2) }}</td>
                                                <td class="text-center">{{ $origin['counter'] }}</td>
                                                <td class="text-center">{{ number_format($origin['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($origin['USD']['total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>TOTAL</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['gran_total'],2) }}</th>
                                            <th class="text-center">{{ $dataOriginSale['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['USD']['total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="box_sections">
                    <h6>Contenido</h6>
                    {{-- Tipo De Vehículo --}}
                    @if ( isset($bookingsStatus) )
                        <a class="option_section active" href="#chartStatus">Por Estatus</a>
                    @endif
                    @if ( isset($dataMethodPayments) )
                        <a class="option_section " href="#chartMethodPayments">Por Metodo De Pago</a>
                    @endif
                    @if ( isset($dataCurrency) )
                        <a class="option_section " href="#chartCurrency">Por Moneda</a>
                    @endif
                    @if ( isset($dataVehicles) )
                        <a class="option_section " href="#chartVehicle">Por Vehículo</a>
                    @endif
                    @if ( isset($dataServiceTypeOperation) )
                        <a class="option_section " href="#chartServiceType">Por Tipo De Servicio</a>
                    @endif

                    @if ( isset($dataSites) )
                        <a class="option_section " href="#chartSites">Por sitio</a>
                    @endif
                    @if ( isset($dataDestinations) )
                        <a class="option_section " href="#chartDestination">Por Destino</a>
                    @endif
                    @if ( isset($dataDriver) )
                        <a class="option_section " href="#chartDrivers">Por Conductor</a>
                    @endif
                    @if ( isset($dataUnit) )
                        <a class="option_section " href="#chartUnits">Por Unidad</a>
                    @endif
                    @if ( isset($dataOriginSale) )
                        <a class="option_section " href="#chartOriginSale">Por Origen De Venta</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>