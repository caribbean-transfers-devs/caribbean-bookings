@php
    use App\Traits\RoleTrait;
    $totals = [
        'QUANTITY' => 0,
        'USD' => 0,
        'MXN' => 0,
        'TOTAL' => 0,
        'TOTAL_CONFIRMED' => 0,
        'TOTAL_PENDING' => 0,
        'COMMISSION' => 0,
    ]
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
            <div class="modal-body commission">
                <div class="container_left">
                    <table class="table table-chart table-chart-general">
                        <thead>
                            <tr>                                                        
                                <th class="text-center">NOMBRE</th>
                                <th class="text-center">CANTIDAD</th>                                    
                                <th class="text-center">USD</th>
                                <th class="text-center">MXN</th>
                                <th class="text-center">TOTAL DE VENTA</th>
                                <th class="text-center">TOTAL OPERADA</th>
                                <th class="text-center">TOTAL PENDIENTE</th>
                                @if ( RoleTrait::hasPermission(96) )
                                    <th class="text-center">COMISIÓN</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @if(sizeof($users) >= 1)
                                @foreach($users as $key => $value)
                                    @php
                                        $total = $value['TOTAL_CONFIRMED'];
                                        $commission = 0;
                                        if($total >= 50000 && $total <= 74999):
                                            //$commission = 2500;
                                            $commission = 0.05 * $total;
                                        endif;
                                        if($total >= 75000 && $total <= 99999):
                                            //$commission = 3750;
                                            $commission = 0.05 * $total;
                                        endif;
                                        if($total >= 100000 && $total <= 124999):
                                            //$commission = 6250;
                                            $commission = 0.05 * $total;
                                        endif;
                                        if($total >= 125000 && $total <= 174999):
                                            //$commission = 8750;
                                            $commission = 0.05 * $total;
                                        endif;
                                        if($total >= 175000):
                                            //$commission = 100000;
                                            $commission = 0.05 * $total;
                                        endif;
                                        $users[$key]['COMMISSION'] = $commission;

                                        $totals['QUANTITY'] += $value['QUANTITY'];
                                        $totals['USD'] += $value['USD'];
                                        $totals['MXN'] += $value['MXN'];
                                        $totals['TOTAL'] += $value['TOTAL'];
                                        $totals['TOTAL_CONFIRMED'] += $value['TOTAL_CONFIRMED'];
                                        $totals['TOTAL_PENDING'] += $value['TOTAL_PENDING'];
                                        $totals['COMMISSION'] += $commission;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $value['NAME'] }}</td>
                                        <td class="text-center">{{ $value['QUANTITY'] }}</td>
                                        <td class="text-center">{{ number_format($value['USD'],2) }}</td>
                                        <td class="text-center">{{ number_format($value['MXN'],2) }}</td>
                                        <td class="text-center">{{ number_format($value['TOTAL'],2) }}</td>
                                        <td class="text-center">{{ number_format($value['TOTAL_CONFIRMED'],2) }}</td>
                                        <td class="text-center">{{ number_format($value['TOTAL_PENDING'],2) }}</td>
                                        @if ( RoleTrait::hasPermission(96) )
                                            <td class="text-center">{{ number_format($commission,2) }}</td>                                                
                                        @endif
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
                                <th class="text-center">{{ number_format($totals['TOTAL_CONFIRMED'],2) }}</th>
                                <th class="text-center">{{ number_format($totals['TOTAL_PENDING'],2) }}</th>
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