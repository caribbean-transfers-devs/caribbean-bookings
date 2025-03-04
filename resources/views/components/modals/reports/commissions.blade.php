@php
    use App\Traits\FiltersTrait;
    use App\Traits\RoleTrait;
    $totals = [
        'TOTAL' => 0,
        'USD' => 0,
        'MXN' => 0,
        'TOTAL_PENDING' => 0,
        'TOTAL_COMPLETED' => 0,
        'TOTAL_DISCOUNT' => 0,
        'TOTAL_LESS_DISCOUNT' => 0,
        'COMMISSION' => 0,
        'QUANTITY' => 0,
    ];
    $PercentageCommissionInvestment = FiltersTrait::PercentageCommissionInvestment();
@endphp
@props(['users'])
<!-- Modal -->
<div class="modal fade" id="commissionsModal" tabindex="-1" role="dialog" aria-labelledby="commissionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commissionsModalLabel">Tabla de comisiones</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="modal-body charts-double">
                <div class="container_left">
                    <table class="table custom-table table-chart table-chart-general">
                        <thead>
                            <tr>                                                        
                                <th class="text-center">NOMBRE</th>
                                <th class="text-center">CANTIDAD</th>                                    
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                                <th class="text-center">TOTAL DE VENTA</th>                                
                                <th class="text-center">TOTAL PENDIENTE</th>
                                <th class="text-center">TOTAL OPERADA</th>
                                <th class="text-center">DESCUENTO POR INVERSION</th>
                                <th class="text-center">TOTAL MENOS DESCUENTO</th>
                                {{-- @if ( RoleTrait::hasPermission(96) ) --}}
                                    <th class="text-center">COMISIÓN</th>
                                {{-- @endif --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if(sizeof($users) >= 1)
                                @foreach($users as $key => $user)
                                    @php
                                        $percentage_commission = ($user['SETTINGS']['type_commission'] === 'target')
                                        ? array_reduce($user['SETTINGS']['targets'], function ($carry, $target) use ($user) {
                                            return ($user['TOTAL_COMPLETED'] >= $target['amount']) ? $target['percentage'] : $carry;
                                        }, 0)
                                        : $user['SETTINGS']['percentage'];

                                        $TotalInvestmentDiscountOperated = round( ($user['TOTAL_COMPLETED'] * ( $PercentageCommissionInvestment / 100 ) ), 2);
                                        $TotalServicesOperatedInvestmentDiscount = round( $user['TOTAL_COMPLETED'] - ($user['TOTAL_COMPLETED'] * ($PercentageCommissionInvestment / 100)), 2);
                                        $TotalCommissionOperated = round(($TotalServicesOperatedInvestmentDiscount * ( $percentage_commission) / 100 ), 2);

                                        $totals['TOTAL'] += $user['TOTAL'];
                                        $totals['USD'] += $user['USD'];
                                        $totals['MXN'] += $user['MXN'];
                                        $totals['TOTAL_PENDING'] += $user['TOTAL_PENDING'];
                                        $totals['TOTAL_COMPLETED'] += $user['TOTAL_COMPLETED'];
                                        $totals['TOTAL_DISCOUNT'] += $TotalInvestmentDiscountOperated;
                                        $totals['TOTAL_LESS_DISCOUNT'] += $TotalServicesOperatedInvestmentDiscount;
                                        $totals['COMMISSION'] += $TotalCommissionOperated;
                                        $totals['QUANTITY'] += $user['QUANTITY'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $user['NAME'] }}</td>
                                        <td class="text-center">{{ $user['QUANTITY'] }}</td>
                                        <td class="text-center">{{ number_format($user['USD'],2) }}</td>
                                        <td class="text-center">{{ number_format($user['MXN'],2) }}</td>
                                        <td class="text-center">{{ number_format($user['TOTAL'],2) }}</td>                                        
                                        <td class="text-center">{{ number_format($user['TOTAL_PENDING'],2) }}</td>
                                        <td class="text-center">{{ number_format($user['TOTAL_COMPLETED'],2) }}</td>
                                        <td class="text-center">{{ number_format($TotalInvestmentDiscountOperated,2) }}</td>
                                        <td class="text-center">{{ number_format($TotalServicesOperatedInvestmentDiscount,2) }}</td>
                                        {{-- @if ( RoleTrait::hasPermission(96) ) --}}
                                            <td class="text-center">{{ number_format($TotalCommissionOperated,2) }}</td>                                                
                                        {{-- @endif --}}
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-center">TOTALES</th>
                                <th class="text-center">{{ $totals['QUANTITY'] }}</th>
                                <th class="text-center">{{ number_format($totals['USD'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['MXN'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['TOTAL'],2) }}</th>                                
                                <th class="text-center">{{ number_format($totals['TOTAL_PENDING'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['TOTAL_COMPLETED'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['TOTAL_DISCOUNT'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['TOTAL_LESS_DISCOUNT'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['COMMISSION'],2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="container_right">
                    <canvas class="chartSale" id="ChartSalesUsers"></canvas>
                </div>                
            </div>
        </div>
    </div>
</div>