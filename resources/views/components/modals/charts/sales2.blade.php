@php
    use App\Traits\BookingTrait;
@endphp
@props(['bookingsStatus','dataMethodPayments','dataCurrency','dataSites','dataOriginSale','dataVehicles','dataDestinations','dataUnit','dataDriver','dataServiceTypeOperation'])
<!-- Modal -->
<div class="modal fade" id="chartsModal2" tabindex="-1" role="dialog" aria-labelledby="chartsModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chartsModalLabel2"></h5>
                <div class="items_status">
                    @if ( isset($bookingsStatus) )
                        @foreach ($bookingsStatus['data'] as $key => $status)
                        <div class="btn btn-{{ BookingTrait::classStatusBooking($key) }}">
                            <span><strong>Total {{ ucfirst(strtolower($status['name'])) }}:</strong> $ {{ number_format($status['gran_total'],2) }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
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
                <div class="container_left">
                    @if ( isset($bookingsStatus) )
                        <div id="chartStatus">                            
                            <div>
                                <div class="chart-container">
                                    <canvas class="chartSale" id="chartSaleStatus2" ></canvas>
                                </div>
                                <div>
                                    <h6>Por Estatus</h6>
                                    <div class="table-responsive">
                                        <table class="table table-chart table-chart-general mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Estatus</th>                                                    
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Pesos</th>
                                                    <th class="text-center">Dolares</th>
                                                    <th class="text-center">Gran Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($bookingsStatus['data'] as $keyStatus => $status )
                                                    <tr>
                                                        <th>{{ ucfirst(strtolower($status['name'])) }}</th>
                                                        <td class="text-center">{{ ucfirst(strtolower($status['counter'])) }}</td>
                                                        <td class="text-center">{{ number_format($status['MXN']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($status['USD']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($status['gran_total'],2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>                                                    
                                                    <th class="text-center">{{ $bookingsStatus['counter'] }}</th>
                                                    <th class="text-center">{{ number_format($bookingsStatus['MXN']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($bookingsStatus['USD']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($bookingsStatus['gran_total'],2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataMethodPayments) )
                        <div id="chartMethodPayments">                            
                            <div>
                                <div class="chart-container">
                                    <canvas class="chartSale" id="chartSaleMethodPayments2" ></canvas>
                                </div>
                                <div>
                                    <h6>Por Metodo De Pago</h6>                                
                                    <div class="table-responsive">
                                        <table class="table table-chart table-chart-general mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Metodo De Pago</th>                                                    
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Pesos</th>
                                                    <th class="text-center">Dolares</th>
                                                    <th class="text-center">Gran Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataMethodPayments['data'] as $keyMethod => $method )
                                                    <tr>
                                                        <th>{{ ucfirst(strtolower($method['name'])) }}</th>
                                                        <td class="text-center">{{ $method['counter'] }}</td>
                                                        <td class="text-center">{{ number_format($method['MXN']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($method['USD']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($method['gran_total'],2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>                                                    
                                                    <th class="text-center">{{ $dataMethodPayments['counter'] }}</th>
                                                    <th class="text-center">{{ number_format($dataMethodPayments['MXN']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataMethodPayments['USD']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataMethodPayments['gran_total'],2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataCurrency) )
                        <div id="chartCurrency">                            
                            <div>
                                <div class="chart-container">
                                    <canvas class="chartSale" id="chartSaleCurrency2" ></canvas>
                                </div>
                                <div>
                                    <h6>Por Moneda</h6>                                
                                    <div class="table-responsive">
                                        <table class="table table-chart table-chart-general mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Moneda</th>                                                    
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Total</th>
                                                    <th class="text-center">Gran Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataCurrency['data'] as $keyCurrency => $currency )
                                                    <tr>
                                                        <th>{{ $currency['name'] }}</th>
                                                        <td class="text-center">{{ $currency['counter'] }}</td>
                                                        <td class="text-center">{{ number_format($currency['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($currency['gran_total'],2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th class="text-center">{{ $dataCurrency['counter'] }}</th>
                                                    <th class="text-center">{{ number_format($dataCurrency['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataCurrency['gran_total'],2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataVehicles) )
                        <div id="chartVehicle">                            
                            <div>
                                <div class="chart-container">
                                    <canvas class="chartSale" id="chartSaleVehicle2" ></canvas>
                                </div>
                                <div>
                                    <h6>Por Vehículo</h6>                                
                                    <div class="table-responsive">
                                        <table class="table table-chart table-chart-general mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Vehículo</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Pesos</th>
                                                    <th class="text-center">Dolares</th>
                                                    <th class="text-center">Gran Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataVehicles['data'] as $keyVehicle => $vehicle )
                                                    <tr>
                                                        <th>{{ ucfirst(strtolower($vehicle['name'])) }}</th>
                                                        <td class="text-center">{{ $vehicle['counter'] }}</td>
                                                        <td class="text-center">{{ number_format($vehicle['MXN']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($vehicle['USD']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($vehicle['gran_total'],2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th class="text-center">{{ $dataVehicles['counter'] }}</th>
                                                    <th class="text-center">{{ number_format($dataVehicles['MXN']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataVehicles['USD']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataVehicles['gran_total'],2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataServiceTypeOperation) )
                        <div id="chartServiceType">                            
                            <div>
                                <div class="chart-container">
                                    <canvas class="chartSale" id="chartSaleServiceType2" ></canvas>
                                </div>
                                <div>
                                    <h6>Por Tipo De Servicio</h6>                                
                                    <div class="table-responsive">
                                        <table class="table table-chart table-chart-general mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tipo De Servicio</th>
                                                    <th class="text-center">Cantidad</th>
                                                    <th class="text-center">Pesos</th>
                                                    <th class="text-center">Dolares</th>
                                                    <th class="text-center">Gran Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dataServiceTypeOperation['data'] as $keyTypeOperation => $typeoperation )
                                                    <tr>
                                                        <th>{{ ucfirst(strtolower($typeoperation['name'])) }}</th>
                                                        <td class="text-center">{{ $typeoperation['counter'] }}</td>
                                                        <td class="text-center">{{ number_format($typeoperation['MXN']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($typeoperation['USD']['total'],2) }}</td>
                                                        <td class="text-center">{{ number_format($typeoperation['gran_total'],2) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>Total</th>
                                                    <th class="text-center">{{ $dataServiceTypeOperation['counter'] }}</th>
                                                    <th class="text-center">{{ number_format($dataServiceTypeOperation['MXN']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataServiceTypeOperation['USD']['total'],2) }}</th>
                                                    <th class="text-center">{{ number_format($dataServiceTypeOperation['gran_total'],2) }}</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="container_right">
                    @if ( isset($dataSites) )
                        <div id="chartSites">
                            <h6>Por Sitio</h6>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>Sitio</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Pesos</th>
                                            <th class="text-center">Dolares</th>
                                            <th class="text-center">Gran Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataSites['data'] as $keySite => $site )
                                            <tr>
                                                <th>{{ ucfirst(strtolower($site['name'])) }}</th>
                                                <td class="text-center">{{ $site['counter'] }}</td>
                                                <td class="text-center">{{ number_format($site['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($site['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($site['gran_total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center">{{ $dataSites['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataSites['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataSites['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataSites['gran_total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataDestinations) )
                        <div id="chartDestination">
                            <h6>Por Destino</h6>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>Destino</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Pesos</th>
                                            <th class="text-center">Dolares</th>
                                            <th class="text-center">Gran Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataDestinations['data'] as $keyDestination => $destination )
                                            <tr>
                                                <th>{{ ucfirst(strtolower($destination['name'])) }}</th>
                                                <td class="text-center">{{ $destination['counter'] }}</td>
                                                <td class="text-center">{{ number_format($destination['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($destination['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($destination['gran_total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center">{{ $dataDestinations['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDestinations['gran_total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataDriver) )
                        <div id="chartDrivers">
                            <h6>Por Conductor</h6>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-driver mb-0">
                                    <thead>
                                        <tr>
                                            <th>Conductor</th>
                                            <th>Unidades</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Pesos</th>
                                            <th class="text-center">Dolares</th>
                                            <th class="text-center">Comisión</th>
                                            <th class="text-center">Gran Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataDriver['data'] as $keyDriver => $driver )
                                            @php
                                                $unitsDriver = '[' . implode(',', $driver['units']) . ']';
                                            @endphp
                                            <tr>
                                                <th>{{ ucfirst(strtolower($driver['name'])) }}</th>
                                                <td>{{ $unitsDriver }}</td>
                                                <td class="text-center">{{ $driver['counter'] }}</td>
                                                <td class="text-center">{{ number_format($driver['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($driver['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($driver['commission'],2) }}</td>
                                                <td class="text-center">{{ number_format($driver['gran_total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th></th>
                                            <th class="text-center">{{ $dataDriver['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['commission'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataDriver['gran_total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataUnit) )
                        <div id="chartUnits">
                            <h6>Por Conductor</h6>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>Unidad</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Pesos</th>
                                            <th class="text-center">Dolares</th>
                                            <th class="text-center">Costo Operativo</th>
                                            <th class="text-center">Gran Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataUnit['data'] as $keyUnit => $unit )
                                            <tr>
                                                <th>{{ ucfirst(strtolower($unit['name'])) }}</th>
                                                <td class="text-center">{{ $unit['counter'] }}</td>
                                                <td class="text-center">{{ number_format($unit['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($unit['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($unit['operating_cost'],2) }}</td>
                                                <td class="text-center">{{ number_format($unit['gran_total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center">{{ $dataUnit['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['operating_cost'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataUnit['gran_total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                    @if ( isset($dataOriginSale) )
                        <div id="chartOriginSale">
                            <h6>Por Origen De Venta</h6>
                            <div class="table-responsive">
                                <table class="table table-chart table-chart-general mb-0">
                                    <thead>
                                        <tr>
                                            <th>Origen</th>
                                            <th class="text-center">Cantidad</th>
                                            <th class="text-center">Pesos</th>
                                            <th class="text-center">Dolares</th>
                                            <th class="text-center">Gran Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dataOriginSale['data'] as $keyOrigin => $origin )
                                            <tr>
                                                <th>{{ ucfirst(strtolower($origin['name'])) }}</th>
                                                <td class="text-center">{{ $origin['counter'] }}</td>
                                                <td class="text-center">{{ number_format($origin['MXN']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($origin['USD']['total'],2) }}</td>
                                                <td class="text-center">{{ number_format($origin['gran_total'],2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Total</th>
                                            <th class="text-center">{{ $dataOriginSale['counter'] }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['MXN']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['USD']['total'],2) }}</th>
                                            <th class="text-center">{{ number_format($dataOriginSale['gran_total'],2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>